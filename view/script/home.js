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
        currentPage: ''
    },
    watch: {
        info: function () {
            clearTimeout(this.timeId);
            this.timeId = setTimeout(() => this.info = '', 4000);
        },
        search: function () {
            this.currentPage = 1;
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
                .then(setTimeout(() => this.search == '' ? this.fetchUsers() : this.fetchSearch(), 100))
                .catch(function (response) { console.log('error', response); });
        },

        switchPage: function (current, index) {
            if (current != index) {
                this.currentPage = index;
                if (this.search == '') this.fetchUsers();
                if (this.search != '') this.fetchSearch();
            }
        },

        fetchSearch: function () {
            const searchInput = new FormData();
            searchInput.append('search', this.search);
            searchInput.append('page', this.currentPage);
            axios({
                method: 'post',
                url: 'user_search',
                data: searchInput,
                config: {
                    headers: { 'Content-Type': 'multipart/form-data' }
                }
            })
                .then((response) => { this.data = response.data; })
                .catch(function (response) { console.log('error', response); });
        },

        fetchUsers: function () {
            const pageInput = new FormData();
            pageInput.append('page', this.currentPage);
            axios({
                method: 'post',
                url: 'user_list',
                data: pageInput,
                config: {
                    headers: { 'Content-Type': 'multipart/form-data' }
                }
            })
                .then((response) => { this.data = response.data; })
                .catch(function (response) { console.log('error', response); });
        }
    },
    created() {
        this.currentPage = 1;
        this.fetchUsers();
    }
});