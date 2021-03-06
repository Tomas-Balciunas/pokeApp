<!DOCTYPE html>
<html>
<?php require "view/_partials/head.view.php"; ?>

<body>
    <?php require "view/_partials/nav.view.php"; ?>

    <div class="container">
        <?php if (!empty($regInfo)) : ?>
            <div>
                <h3 class="center"><?= $regInfo; ?></h3>
            </div>
        <?php endif; ?>

        <div class="row">
            <form method="POST">
                <div class="row">
                    <div class="col l6 s12 input-field offset-l3">
                        <input id="login" type="text" name="loginName" placeholder="Name" value="<?php echo isset($filledData['loginName']) ? $filledData['loginName'] : ''; ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col l6 s12 input-field offset-l3">
                        <input id="password" type="password" name="loginPassword" placeholder="Password">
                    </div>
                </div>
                <div class="row">
                    <div class="centerItem">
                        <button class="btn waves-effect waves-light" type="submit" name="loginBtn">Log in</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row centerItem">
            <?php if (!empty($error)) : ?>
                <div class="logErr">
                    <p class="errText"><?= $error; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="container centerItem">
            <form method="POST">
                <div class="row">
                    <div class="col l6 s6">
                        <input type="text" name="registerName" placeholder="Name" value="<?php echo isset($filledData['registerName']) ? $filledData['registerName'] : ''; ?>">
                    </div>
                    <div class="col l6 s6">
                        <input type="text" name="registerLastname" placeholder="Last Name" value="<?php echo isset($filledData['registerLastname']) ? $filledData['registerLastname'] : ''; ?>">
                    </div>
                </div>
                <div class="row centerItem centerItem">
                    <div class="col s12">
                        <input type="text" name="registerEmail" placeholder="Email" value="<?php echo isset($filledData['registerEmail']) ? $filledData['registerEmail'] : ''; ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col l6 s12">
                        <input type="password" name="registerPassword" placeholder="Password">
                    </div>
                    <div class="col l6 s12">
                        <input type="password" name="registerPasswordRepeat" placeholder="Repeat password">
                    </div>

                </div>
                <div class="row centerItem">
                    <div>
                        <button class="btn waves-effect waves-light" type="submit" name="registerBtn">Register</button>
                    </div>
                </div>

            </form>
        </div>

        <div class="row centerItem">
            <div>
            <?php if (!empty($validation)) : ?>
                <?php foreach ($validation as $val) : ?>
                    <p class="errText"><?= $val; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
        
    </div>
    <script type="text/javascript" src="view/script/js/bin/materialize.min.js"></script>
</body>

</html>