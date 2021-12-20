<!DOCTYPE html>
<html>
<?php require "view/_partials/head.view.php"; ?>

<body>
    <?php require "view/_partials/nav.view.php"; ?>
    <div id="app">
        <table>
            <tr>
                <th>Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Pokes</th>
                <th>Send a poke</th>
            </tr>
            <template v-for="(e, index) in data">
                <tr>
                    <td>{{e.name}}</td>
                    <td>{{e.last_name}}</td>
                    <td>{{e.email}}</td>
                    <td>{{e.pokes}}</td>
                    <td><button @click="pokeUser(e)">Poke</button></td>
                </tr>
            </template>
        </table>
        <?php require "view/_partials/info.view.php"; ?>
    </div>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="module" src="view/script/home.js"></script>
</body>

</html>