<?php
require_once ('./back/php/varable_session.php');
require_once('./back/php/login_form.php')


?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="./css/updatedloginstyle.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

        <!-- message shower with  notyf-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    </head>

    <body>
        <div class="wraper">
            <form action="./login.php" method="post" name="form" onsubmit="return validated()">
                <h1>Login</h1>
                <div class="input-box"><input type="text" placeholder="username" name="username">
                    <i class='bx bxs-user'></i>
                </div>
                <div id="error_username">
                    Please enter your username
                </div>
                <div class="input-box"><input type="password" placeholder="password" name="password">
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div id="error_password">
                    Please enter your password
                </div>
                <!-- <div class="forgot">
            <a href="#">Forgot password ?</a>
            </div> -->
                <button type="submit" class="btn">Login</button>

            </form>
        </div>
        <script src="./front/js/loginjs.js"></script>

        <!-- sicript with notyf -->
        <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>




        <script type="module">
        import NotyfService from "./front/js/message.shower.js";

        <?php if (isset($message)): ?>
        const message = JSON.parse('<?php echo json_encode($message)?>')
        NotyfService.showMessage(message.status, message.message);

        <?php endif;
        $message = null;
        ?>
        </script>
    </body>

</html>