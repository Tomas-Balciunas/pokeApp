<!DOCTYPE html>
<html>
<?php require "view/_partials/head.view.php"; ?>

<body>
    <?php require "view/_partials/nav.view.php"; ?>

    <?php if (!empty($regInfo)): ?>
    <div>
        <h3><?= $regInfo; ?></h3>
    </div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="text" name="loginName" placeholder="Name">
        <input type="password" name="loginPassword" placeholder="Password">
        <input type="submit" name="loginBtn" value="Log in">
    </form>

    <?php if (!empty($error)): ?>
    <div class="logErr">
        <p class="errText"><?= $error; ?></p>
    </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="registerName" placeholder="Name">
        <input type="text" name="registerLastname" placeholder="Last Name">
        <input type="text" name="registerEmail" placeholder="Email">
        <input type="password" name="registerPassword" placeholder="Password">
        <input type="password" name="registerPasswordRepeat" placeholder="Repeat password">
        <input type="submit" name="registerBtn" value="Register">
    </form>

    <?php if (!empty($validation)): ?>
        <?php foreach($validation as $val): ?>
            <p class="errText"><?= $val; ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>