new Vue({
    el: '#profile',
    data() {
        return {
            data: '',
            userData: '',
            info: '',
            error: '',
            notifShow: false,
            currentPage: '',
            search: '',
            type: 'pokes',
            notifications: '',
            updatedNotif: '',
            newNotif: false
        }
    },
    watch: {
        info: function () {
            clearTimeout(this.timeId);
            this.timeId = setTimeout(() => this.info = '', 4000)
        },
        search: function () {
            this.currentPage = 1;
        },
        updatedNotif: function () {
            const isEqual = (...objects) => objects.every(obj => JSON.stringify(obj) === JSON.stringify(objects[0]));
            if (!isEqual(this.notifications, this.updatedNotif)) {
                this.newNotif = true
                this.notifications = this.updatedNotif
            }
        }
    },
    methods: {
        update: function () {
            const updateForm = new FormData(document.getElementById('updateForm'));
            axios({
                method: 'post',
                url: 'profile/update',
                data: updateForm,
                config: {
                    headers: { 'Content-Type': 'multipart/form-data' }
                }
            })
                .then((data) => {
                    data.data.info != '' ? this.info = data.data.info : this.info = '';
                    data.data.vali != '' ? this.error = data.data.vali : this.error = '';
                })
                .then(setTimeout(() => this.fetchUser(), 200))
                .catch(function (response) { console.log('error', response); });
            this.$refs["passold"].value = '';
            this.$refs["passnew"].value = '';
            this.$refs["passrepeat"].value = '';
        },

        toggleNotif: function () {
            this.notifShow = !this.notifShow;
            this.newNotif = false;
        },

        switchPage: function (current, index) {
            if (current != index) {
                this.currentPage = index;
                if (this.search == '') this.fetchUser();
                if (this.search != '') this.fetchSearch();
            }
        },

        fetchNotifs: function () {
            axios.get('notifications').then((response) => {
                this.notifications = response.data;
            }).catch((error) => {
                console.log(error);
            })
        },

        updateNotifs: function () {
            axios.get('notifications').then((response) => {
                this.updatedNotif = response.data;
            }).catch((error) => {
                console.log(error);
            })
        },

        fetchSearch: function () {
            const searchInput = new FormData();
            searchInput.append('search', this.search);
            searchInput.append('page', this.currentPage);
            searchInput.append('type', this.type);
            axios({
                method: 'post',
                url: 'search',
                data: searchInput,
                config: {
                    headers: { 'Content-Type': 'multipart/form-data' }
                }
            })
                .then((response) => { this.data = response.data.data; })
                .catch(function (response) { console.log('error', response); });
        },

        fetchUser: function () {
            const pageInput = new FormData();
            pageInput.append('page', this.currentPage);
            axios({
                method: 'post',
                url: 'user',
                data: pageInput,
                config: {
                    headers: { 'Content-Type': 'multipart/form-data' }
                }
            })
                .then((response) => {
                    this.userData = response.data.user;
                    this.data = response.data.data;
                })
                .catch(function (response) { console.log('error', response); });
        }
    },
    created() {
        this.currentPage = 1;
        this.fetchUser();
        this.fetchNotifs();
        this.timer = setInterval(this.updateNotifs, 10000)
    }
});