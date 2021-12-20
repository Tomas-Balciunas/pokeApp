<!DOCTYPE html>
<html>
<?php require "view/_partials/head.view.php"; ?>

<body>
    <?php require "view/_partials/nav.view.php"; ?>
    <div id="profile" v-if="data">
        <form method="POST" @submit.prevent="update()" id="updateForm">
            <input type="text" :value="data.user.name" disabled>
            <input type="text" name="updateLastname" :value="data.user.last_name">
            <input type="text" name="updateEmail" :value="data.user.email">
            <input type="password" name="updatePasswordOld" placeholder="Current Password" ref="passold">
            <input type="password" name="updatePasswordNew" placeholder="New Password" ref="passnew">
            <input type="password" name="updatePasswordNewRepeat" placeholder="Repeat New Password" ref="passrepeat">
            <input type="submit" name="updateBtn" value="Update">
        </form>

        <template v-for="e in data.pokes">
            <p>{{e.from_user_name}} - {{e.time_sent}}</p>
        </template>
        <?php require "view/_partials/info.view.php"; ?>
    </div>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="module" src="view/script/profile.js"></script>
</body>

</html>