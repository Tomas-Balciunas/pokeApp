<div class="navContainer grey darken-4 z-depth-2">
    <?php if (isset($_SESSION['user_id'])) : ?>

        <div class="btnSet">
            <a href="/sonaro" class="btn col s12"><i class="small material-icons left">home</i>Home</a>
            <a href="/sonaro/profile" class="btn"><i class="small material-icons left">settings</i>Profile</a>
        </div>

        <div class="btnSet">
            <template>
                <div class="notifHolder">
                    <div v-if="newNotif" @click="toggleNotif" class="btn-floating pulse red"><i class="small material-icons left">notifications</i></div>
                    <div v-else @click="toggleNotif" class="btn-floating"><i class="small material-icons left">notifications</i></div>
                    <div class="notifContent z-depth-5 grey darken-4" v-if="notifShow">
                        <p class="center">Recent pokes</p>
                        <template v-if="notifications" v-for="e in notifications">
                            <p>User <span class="teal-text text-lighten-2"><b>{{e.from_user_name}}</b></span> has poked you on {{e.time_sent}}</p>
                        </template>
                        <p class="center"><a href="/sonaro/profile">All Pokes</a></p>
                    </div>
                </div>
            </template>

            <a href="/sonaro/data">
                <div class="btn-floating"><i class="small material-icons left">attach_file</i></div>
            </a>
        </div>

        <div class="btnSet">
            <a href="/sonaro/logout" class="btn"><i class="small material-icons left">logout</i>Log Out</a>
        </div>

    <?php else : ?>
        <h5 class="white-text center-align">Welcome to the poke app, please log in to continue</h5>
    <?php endif; ?>
</div>