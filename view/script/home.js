new Vue({
    el: '#app',
    data: {
        data: '',
        info: '',
        poke: {
            id: '',
            name: '',
            email: ''
        }
    },
    watch: {
        info: function () {
            clearTimeout(this.timeId);
            this.timeId = setTimeout(() => this.info = '', 4000);
        }
    },
    methods: {
        pokeUser: function (e) {
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
                .then(setTimeout(() => this.fetchUsers(), 200))
                .catch(function (response) { console.log('error', response); });
        },


        fetchUsers: function () {
            axios.get('user_list').then((response) => {
                this.data = response.data;
            }).catch((error) => {
                console.log(error);
            });
        }
    },
    created() {
        this.fetchUsers();
    }
});