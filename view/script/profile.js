new Vue({
    el: '#profile',
    data () {
        return {
            data: '',
            info: '',
        }
    },
    watch: {
        info: function () {
            clearTimeout(this.timeId);
            this.timeId = setTimeout(() => this.info = '', 4000)
        }
    },
    methods: {
        update: function () {
            axios({
                method: 'post',
                url: 'profile/update',
                data: new FormData(document.getElementById('updateForm')),
                config: {
                    headers: { 'Content-Type': 'multipart/form-data' }
                }
            })
                .then((data) => { this.info = data.data })
                .then(setTimeout(() => this.fetchUser(), 200))
                .catch(function (response) { console.log('error', response); });
            this.$refs["passold"].value = '';
            this.$refs["passnew"].value = '';
            this.$refs["passrepeat"].value = '';
        },

        fetchUser: function () {
            axios.get('user').then((response) => {
                this.data = response.data;
            }).catch((error) => {
                console.log(error);
            });
        }
    },
    created() {
        this.fetchUser();
    }
});