<!DOCTYPE html>
<html>
<?php require "view/_partials/head.view.php"; ?>

<body>
    <div id="data">
        <?php require "view/_partials/nav.view.php"; ?>

        <div class="progressCont">
            <div v-if="loading" class="progress">
                <div class="indeterminate"></div>
            </div>
        </div>

        <div class="container">

            <div class="row centerItem">
                <?php if (!empty($info)) : ?>
                    <h6 v-model="info" class="text purple-text text-lighten-2"><?= $info; ?></h6>
                <?php endif; ?>
            </div>

            <div class="row">
                <h5 class="center">Import users from a .csv file</h5>
            </div>
            <div class="row centerItem">
                <form method="post" enctype="multipart/form-data">
                    <input class="btn" type="file" name="csv" accept=".csv">
                    <input class="btn" type="submit" @click="loadingNotif()" name="import" value="Import Data">
                </form>
            </div>

            <div class="row">
                <h5 class="center">Import pokes from a .json file</h5>
            </div>
            <div class="row centerItem">
                <form method="post" enctype="multipart/form-data">
                    <input class="btn" type="file" name="json" accept=".json">
                    <input class="btn" type="submit" @click="loadingNotif()" name="importPokes" value="Import Data">
                </form>
            </div>

            <div class="row centerItem">
                <h5>Generate and export poke data from current users in .json format</h5>
            </div>
            <div class="row centerItem">
                <form method="post">
                    <input class="btn" type="submit" name="generate" value="Generate Pokes">
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="module" src="view/script/data.js"></script>
</body>

</html>