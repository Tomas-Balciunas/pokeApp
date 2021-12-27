new Vue({
    el: '#data',
    data: {
        loading: false,
        notifShow: false,
        notifications: '',
        updatedNotif: '',
        newNotif: false
    },
    watch: {
        updatedNotif: function () {
            const isEqual = (...objects) => objects.every(obj => JSON.stringify(obj) === JSON.stringify(objects[0]));
            if (!isEqual(this.notifications, this.updatedNotif)) {
                this.newNotif = true
                this.notifications = this.updatedNotif
            }
        }
    },
    methods: {
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
    },
    created() {
        this.fetchNotifs();
        this.timer = setInterval(this.updateNotifs, 10000)
    }
});