<?php
    session_start();

    @include('db.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception; 
    
    $con = mysqli_connect($servername, $username, $password, $database);

    $newPassword = $confirmPassword = $passError = $error = $email = '';
    $status = 0;

    if($_GET['id']){
        $status = $_GET['id'];
    }

    function sendMail($email){
        require 'PhpMailer/PHPMailer.php';
        require 'PhpMailer/SMTP.php';
        require 'PhpMailer/Exception.php';

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();                                           
            $mail->Host       = 'smtp.gmail.com';                    
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = 'openmail12345678@gmail.com';                     
            $mail->Password   = 'dscqqpxoeschccez';                               
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port       = 465;                                    
        
            //Recipients
            $mail->setFrom('openmail12345678@gmail.com', 'Prasad');   
            $mail->addAddress($email);               
        
            //Content
            $mail->isHTML(true);                                  
            $mail->Subject = 'Reset your password';
            $mail->Body    = 'http://localhost/UserFunction/internship/project-1/forgot_pwd.php?id=2';
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }                     

    if(isset($_POST['sendLink'])){
        $_SESSION['email'] = $_POST['email'];
        $email = $_SESSION['email'];
        sendMail($email);
        $status = 1;
    }
    
    if(isset($_POST['resendLink'])){
        $status = 0;
    }

    if(isset($_POST['reset'])){
        $newPassword = $_POST['new-pass'];
        $confirmPassword = $_POST['confirm-pass'];
        $email = $_SESSION['email'];

        if(empty($newPassword) && empty($confirmPassword)){
            $passError="Password cannot be empty!";
            $error="Password cannot be empty!";
            // $status = 2;
        }
        else if(empty($newPassword)){
            $passError="Password cannot be empty!";
            // $status = 2;
        }else if(empty($confirmPassword)){
            $error="Password cannot be empty!";
            // $status = 2;
        }else {
            if (strlen($newPassword)<8) {
                $passError="Password must contain at least 8 character";
                // $status = 2;
            }else if(!preg_match("/[A-Z]+/", $newPassword)){
                $passError = "At least one uppercase letter is required!";
                // $status = 2;
            }else if(!preg_match("/[a-z]+/", $newPassword)){
                $passError = "At least one lowercase letter is required!";
                // $status = 2;
            }else if(!preg_match("/[^\w\s]+/", $newPassword)){
                $passError = "At least one special character is required!";
                // $status = 2;
            }else if($newPassword!=$confirmPassword){
                $error= "Confirm Password does not match!";   
                // $status = 2;
            }else{

                $sql3 = "SELECT * FROM `user` WHERE email = '$email' ";
                $result3 = mysqli_query($con, $sql3);
                if(mysqli_num_rows($result3) != 0){
                    $sql4 = "UPDATE `user` SET password = '$newPassword' WHERE email = '$email' ";
                    $result4 = mysqli_query($con, $sql4);
                    ?>
                        <script>
                            alert('Your password has been changed successfully!');
                            window.location = "login.php";
                        </script>
                    <?php
                }else{
                    // header('Loaction: forgot_pwd.php');
                    ?>
                    <script>
                        alert('Your email address is not registred! Please register.');
                        window.location = "registration.php";
                    </script>
                <?php
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

        <!-- font-family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/style.css">

    <style>
            input:-webkit-autofill,
            input:-webkit-autofill:hover,
            input:-webkit-autofill:focus,
            input:-webkit-autofill:active {
                transition: background-color 5000s ease-in-out 0s;
            }
    </style>

</head>
<body>
<section class="forgot_pwd">

    <?php

        if($status == 0){
            ?>
                <div class="form-box">
                    <h3>Reset your password</h3>
                    <p class="text-muted">Get link to reset your password.</p>
                    <form method="POST">
                        <div class="floating">
                            <input type="text" class="control" name="email" id="email" required>
                            <label class="text-muted" for="email">Email address</label>
                        </div>
                        <div class="sbt-btn">
                            <input type="submit" name="sendLink" value="continue" class="submit">
                        </div>
                    </form>
                    <div class="back">
                        <a href="login.php">Back To Login</a>
                    </div>
                </div>
            <?php
        }
    ?>
    
    <?php

        if($status == 1){
            ?>
                <div class="form-box">
                    <div class="icon">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <h3>Check your email</h3>
                    <p class="text-muted">Please check the email for link to reset your password. </p>
                    <form method="POST">
                        <div class="sbt-btn">
                            <input type="submit" name="resendLink" value="resend link" class="submit">
                        </div>
                    </form>
                </div>
            <?php
        }
    ?>
    
    <?php

        if($status == 2){
            ?>
                <div class="form-box">
                    
                    <h3>Reset your password</h3>
                    <p class="text-muted">Create a new password.</p>
                    <form action="#" method="POST">
                        <div class="floating">
                            <span class="eye" id="hide" onclick="toggle('new-pass', 'np-eye', 'nps-eye')">
                                <i class="fa-sharp fa-regular fa-eye" id="np-eye"></i>
                                <i class="fa-sharp fa-regular fa-eye-slash" id="nps-eye"></i>
                            </span>
                            <input type="password" class="control" name="new-pass" id="new-pass" value="<?php echo $newPassword?>" required>
                            <label class="text-muted" for="new-pass">New password</label>
                        </div>
                        <div class="error">
                            <span id="error"><?php echo $passError?></span>
                        </div>
                        <div class="floating">
                            <span class="eye" id="hide" onclick="toggle('confirm-pass', 'cp-eye', 'cps-eye')">
                                <i class="fa-sharp fa-regular fa-eye" id="cp-eye"></i>
                                <i class="fa-sharp fa-regular fa-eye-slash" id="cps-eye"></i>
                            </span>
                            <input type="password" class="control" name="confirm-pass" id="confirm-pass" value="<?php echo $confirmPassword?>" required>
                            <label class="text-muted" for="confirm-pass">Confirm password</label>
                        </div>
                        <div class="error">
                            <span id="error"><?php echo $error?></span>
                        </div>
                        <div class="sbt-btn">
                            <input type="submit" name="reset" value="Reset Password" class="submit">
                        </div>
                    </form>
                </div>
            <?php
        }
    ?>
</section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>

    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
            }

        const toggle = (id, nid, cid) => {
            const x = document.getElementById(id);
            const y = document.getElementById(nid);
            const z = document.getElementById(cid);

            if(x.type == 'password'){
            x.type = "text";
            y.style.display = "block";
            z.style.display = "none";
            }else{
            x.type = "password";
            y.style.display = "none";
            z.style.display = "block";
            }
      }
    </script>
    <script src="https://kit.fontawesome.com/9d09e1757b.js" crossorigin="anonymous"></script>
</body>
</html>