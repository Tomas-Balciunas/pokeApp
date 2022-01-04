new Vue({
    el: '#app',
    data: {
        data: '',
        info: '',
        search: '',
        poke: {
            id: '',
            name: '',
            email: ''
        },
        currentPage: '',
        notifShow: false,
        type: 'users',
        notifications: '',
        updatedNotif: '',
        newNotif: false,
        loading: false
    },
    watch: {
        info: function () {
            clearTimeout(this.timeId);
            this.timeId = setTimeout(() => this.info = '', 4000);
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
        },
        data: function () {
            this.loading = false;
        }
    },
    methods: {
        pokeUser: function (e) {
            this.loading = true;
            this.poke.id = e.id;
            this.poke.name = e.name;
            this.poke.email = e.email;
            const pokeData = new FormData();
            pokeData.append('id', this.poke.id);
            pokeData.append('name', this.poke.name);
            pokeData.append('email', this.poke.email);

            axios({
                method: 'post',
                url: 'poke',
                data: pokeData,
                config: {
                    headers: { 'Content-Type': 'multipart/form-data' }
                }
            })
                .then((data) => { this.info = data.data })
                .then(setTimeout(() => this.fetchUsers(), 100))
                .catch(function (response) { console.log('error', response); });
            
        },

        switchPage: function (current, index) {
            if (current != index) {
                this.currentPage = index;
                this.fetchUsers();
            }
        },

        toggleNotif: function () {
            this.notifShow = !this.notifShow;
            this.newNotif = false;
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

        fetchUsers: function () {
            const pageInput = new FormData();
            pageInput.append('page', this.currentPage);
            pageInput.append('search', this.search);
            pageInput.append('type', this.type);
            axios({
                method: 'post',
                url: 'user_list',
                data: pageInput,
                config: {
                    headers: { 'Content-Type': 'multipart/form-data' }
                }
            })
                .then((response) => { this.data = response.data.data; })
                .catch(function (response) { console.log('error', response); });
        }
    },
    created() {
        this.currentPage = 1;
        this.fetchUsers();
        this.fetchNotifs();
        this.timer = setInterval(this.updateNotifs, 10000)
    }
});