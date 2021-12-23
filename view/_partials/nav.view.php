<div class="navContainer grey darken-4 z-depth-2">
    <?php if (isset($_SESSION['user_id'])) : ?>
        <a href="/sonaro" class="btn"><i class="small material-icons left">home</i>Home</a>

        <a href="/sonaro/profile" class="btn"><i class="small material-icons left">settings</i>Profile</a>

        <template v-if="data">
            <div class="notifHolder col s1">
                <div v-if="newNotif" @click="toggleNotif" class="btn-floating pulse red"><i class="small material-icons left">notifications</i></div>
                <div v-else @click="toggleNotif" class="btn-floating"><i class="small material-icons left">notifications</i></div>
                <div class="notifContent z-depth-5 grey darken-4" v-if="notifShow">
                    <p class="center">Recent pokes</p>
                    <template v-for="e in notifications">
                        <p>User <span class="teal-text text-lighten-2"><b>{{e.from_user_name}}</b></span> has poked you on {{e.time_sent}}</p>
                    </template>
                    <p class="center"><a href="/sonaro/profile">All Pokes</a></h4>
                </div>
            </div>
        </template>

        <a href="/sonaro/logout" class="btn"><i class="small material-icons left">logout</i>Log Out</a>

    <?php else : ?>
        <h4>Welcome to the poke app, please log in to continue</h4>
    <?php endif; ?>
</div>