<!DOCTYPE html>
<html>
<?php require "view/_partials/head.view.php"; ?>

<body>
    <div id="profile">
        <?php require "view/_partials/nav.view.php"; ?>
        <div class="container">

            <div class="row">
                <form method="POST" @submit.prevent="update()" id="updateForm">
                    <div class="row">
                        <div class="col l4 s6 ">
                            <input type="text" :value="userData.name" disabled>
                        </div>
                        <div class="col l4 s6">
                            <input type="text" name="updateLastname" :value="userData.last_name">
                        </div>
                        <div class="col l4 s12">
                            <input type="text" name="updateEmail" :value="userData.email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col l4 s12">
                            <input type="password" name="updatePasswordOld" placeholder="Current Password" ref="passold">
                        </div>
                        <div class="col l4 s12">
                            <input type="password" name="updatePasswordNew" placeholder="New Password" ref="passnew">
                        </div>
                        <div class="col l4 s12">
                            <input type="password" name="updatePasswordNewRepeat" placeholder="Repeat New Password" ref="passrepeat">
                        </div>
                    </div>
                    <div class="row centerItem">
                        <input type="submit" class="btn" name="updateBtn" value="Update">
                    </div>
                </form>

                <div class="row centerItem">
                    <div>
                        <template v-if="error">
                            <p class="errText" v-for="e in error">{{e}}</p>
                        </template>
                    </div>
                </div>

            </div>

            <div class="row centerItem">
                <div class="input-field">
                    <i class="material-icons prefix">search</i>
                    <input id="searchUsers" type="text" v-model="search" @keyup="fetchUser()">
                    <label for="searchUsers">Search Pokes</label>
                </div>
            </div>

            <div class="row">
                    <table class="table highlight striped col l8 offset-l2 m8 offset-m2">
                        <tr class="grey darken-4 white-text">
                            <th class="center">From User</th>
                            <th class="center">Time Poked</th>
                        </tr>
                        <template v-for="e in data.data">
                            <tr>
                                <td>{{e.name}}</td>
                                <td class="center">{{e.time_sent}}</td>
                            </tr>
                        </template>
                    </table>
            </div>

            <div v-if="data.pages" class="centerItem">
                <div>
                <ul class="pagination">
                    <template v-for="index in data.pages.all">
                        <li v-if="currentPage == index" class="active" @click="switchPage(data.pages.current, index)"><a href="#!">{{index}}</a></li>
                        <li v-else class="waves-effect" @click="switchPage(data.pages.current, index)"><a href="#!">{{index}}</a></li>
                    </template>
                </ul>
                </div>
            </div>

        </div>
        <?php require "view/_partials/info.view.php"; ?>
    </div>

    <script type="text/javascript" src="view/script/js/bin/materialize.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="module" src="view/script/profile.js"></script>
</body>

</html>