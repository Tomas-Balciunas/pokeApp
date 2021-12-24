<!DOCTYPE html>
<html>
<?php require "view/_partials/head.view.php"; ?>

<body>
    <div id="data">
        <?php require "view/_partials/nav.view.php"; ?>
        <div class="container">
            <div class="row">
                <h5 class="center">Import users from a .csv file</h5>
            </div>
            <div class="row centerItem">
                <form method="post" enctype="multipart/form-data">
                    <input class="btn" type="file" name="csv" accept=".csv">
                    <input class="btn" type="submit" name="import" value="Import Data">
                </form>
            </div>
            <div class="row centerItem">
                <?php if (!empty($info)) : ?>
                    <p><?= $info; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="module" src="view/script/data.js"></script>
</body>

</html>