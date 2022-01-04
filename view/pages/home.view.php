<!DOCTYPE html>
<html>
<?php require "view/_partials/head.view.php"; ?>

<body>
    <div id="app">
        <?php require "view/_partials/nav.view.php"; ?>

        <div class="progressCont">
            <div v-if="loading" class="progress">
                <div class="indeterminate"></div>
            </div>
        </div>

        <div class="container">

            <div class="row centerItem">
                <div class="input-field">
                    <i class="material-icons prefix">search</i>
                    <input id="searchUsers" type="text" v-model="search" @keyup="fetchUsers()">
                    <label for="searchUsers">Search Users</label>
                </div>
            </div>

            <div class="row">
                <table class="table highlight responsive-table">
                    <tr class="grey darken-4 white-text">
                        <th>Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th class="center">Pokes</th>
                        <th class="center">Send a poke</th>
                    </tr>
                    <template v-for="(e, index) in data.data">
                        <tr>
                            <td>{{e.name}}</td>
                            <td>{{e.last_name}}</td>
                            <td>{{e.email}}</td>
                            <td class="center">{{e.pokes}}</td>
                            <td><a @click="pokeUser(e)" class="waves-effect waves-light btn"><i class="material-icons left">touch_app</i>Poke</a></td>
                        </tr>
                    </template>
                </table>
            </div>

            <div v-if="data.pages" class="row centerItem">
                <ul class="pagination">
                    <template v-for="index in data.pages.all">
                        <li v-if="currentPage == index" class="active" @click="switchPage(data.pages.current, index)"><a href="#!">{{index}}</a></li>
                        <li v-else class="waves-effect" @click="switchPage(data.pages.current, index)"><a href="#!">{{index}}</a></li>
                    </template>
                </ul>
            </div>

        </div>

        <?php require "view/_partials/info.view.php"; ?>
    </div>

    <script type="text/javascript" src="view/script/js/bin/materialize.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="module" src="view/script/home.js"></script>
</body>

</html>