<?php

    @include 'db.php';

    session_start();
    
    if(!$_SESSION['logged']){
        header('location : login.php');
    }

    $email = $_SESSION['email'];
    $status = 0;
    $confirmPassword = $newPassword = $error = $passError = '';
    // Create connection
    $con = mysqli_connect($servername, $username, $password, $database);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;  


    function sendMail($otp, $email){
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
            $mail->Subject = 'Email verification with One Time Password';
            $mail->Body    = 'One Time Password : '. $otp .'. This one time password will only valid for 60 seconds!';
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    if(isset($_POST['get-otp'])){
        // if($_POST['email']){
        //     $email = $_POST['email'];
        // }else{
        //     $emailError = "Please enter email!";
        // }

        $otp = random_int(111111, 999999);
        $flag = sendMail($otp, $email);
        
        if($flag){
            $sql = "UPDATE `user` SET `otp`= $otp, `date_time` = now() WHERE email = '$email' ";
            $result =  mysqli_query($con, $sql);
            $status = 1;
            echo '<script>alert("OTP has been sent to your email address!. Please check your Email!")</script>';
        }else{
            echo '<script>alert("Invalid Email adress!")</script>';
            $status = 0;
        }
    }


    if(isset($_POST['enter-otp'])){
        $otp2 = $_POST['otp'];
        if($otp2 != ''){
            $sql2 = "SELECT * FROM `user` WHERE otp = $otp2 AND now() <= date_add(date_time, INTERVAL 1 MINUTE) ";
            $result2 =  mysqli_query($con, $sql2);
            $row = mysqli_fetch_assoc($result2);
            if($row){
                // $email = $_POST['email'];
                $status = 2;
            }else{
                echo "<script>alert('Invalid OTP!')</script>";
                $status = 0;
            }
        }else{
            // $email = $_POST['email'];
            $error = 'Please enter OTP';
            $status = 1;
        }
    }

    if(isset($_POST['save'])){
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if(empty($newPassword) && empty($confirmPassword)){
            $passError="Password cannot be empty!";
            $error="Password cannot be empty!";
            $status = 2;
        }
        else if(empty($newPassword)){
            $passError="Password cannot be empty!";
            $status = 2;
        }else if(empty($confirmPassword)){
            $error="Password cannot be empty!";
            $status = 2;
        }else {
            if (strlen($newPassword)<8) {
                $passError="Password must contain at least 8 character";
                $status = 2;
            }else if(!preg_match("/[A-Z]+/", $newPassword)){
                $passError = "Password must contain at least one uppercase letter!";
                $status = 2;
            }else if(!preg_match("/[a-z]+/", $newPassword)){
                $passError = "Password must contain at least one lowercase letter!";
                $status = 2;
            }else if(!preg_match("/[^\w\s]+/", $newPassword)){
                $passError = "Password must contain at least one special character!";
                $status = 2;
            }else if($newPassword!=$confirmPassword){
                $error= "Confirm Password does not match!";   
                $status = 2;
            }else{
                $sql3 = "UPDATE `user` SET password = '$newPassword' WHERE email = '$email' ";
                $result3 = mysqli_query($con, $sql3);
                if($result3){
                    $_SESSION['status'] = true;
                    header('location: profile.php');
                }else{
                    $_SESSION['status'] = false;
                    header('loaction: password_update.php');
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
    <title>Password Update</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
    <!-- font-family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>

<body>
    <!-- navbar start -->
    <section class="nav-bar">
        <nav class="navbar navbar-expand">
        <div class="container">
        <div class="logo">
            <a href="profile.php" ><img id="main-logo" src="assets/images/PROFILE-bg.png" /></a>
        </div>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="profile.php" class="nav-link active ms-2 me-2 fw-bold">Profile</a>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link active fw-bold" id="update" href="#" aria-expanded="false">
                Update
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="profile_update.php"  name="password">Profile</a></li>
            </ul>
            <!-- <a href="admin.php" id="admin" class="nav-link active fw-bold">Admin</a> -->
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link active ms-2 me-2 fw-bold" onclick="confirmLogout()">Logout</a>
            </li>
        </ul>
        </div>
        </nav>
    </section>
    <!-- navbar End -->

    <!-- password update area -->
    <section class="pass_update">
    <div class="container ">
        <div class="row d-flex justify-content-center ">
            <div class="col-md-6 pass-data-area">
                <form action="#" method="POST">
                    <div class="pass-data">
                        <label for="email" class="form-label">Email : </label>
                        <input type="text" class="form-control" name="email" id="email" value="<?php echo $email?>" readonly >
                    </div>

                    <!-- status 0 -->
                    <?php
                        if ($status == 0){
                    ?>
                    <div class="d-flex justify-content-center mt-4 get-otp-btn">
                        <button class="btn" name="get-otp">Get OTP</button>               
                        </div>                   
                    </div>
                    <?php
                        }
                    ?>

                    <!-- status 1 -->
                    <?php
                        if ($status == 1){
                    ?>
                    <div class="pass-data">
                        <label for="phone" class="form-label">OTP : </label>
                        <input type="text" class="form-control" name="otp" placeholder="Enter OTP">
                        <span id="error" ><?php echo $error?></span>
                    </div>
                    <div class="d-flex justify-content-center mt-4 enter-otp-btn">
                        <button class="btn" name="enter-otp">Enter OTP</button>               
                        </div>                   
                    </div>
                    <?php
                        }
                    ?>
                    

                    <!-- status 2 -->
                    <?php
                        if ($status == 2){
                    ?>
                    <div class="pass-data">
                        <label for="Enter New Password" class="form-label">Enter New Password : </label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" value="<?php echo $newPassword?>">
                        <span id="error"><?php echo $passError?></span>
                    </div>
                    <div class="pass-data">
                        <label for="Confirm Password" class="form-label">Confirm Password : </label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" value="<?php echo $confirmPassword?>">
                        <span id="error"><?php echo $error?></span>
                    </div>
                    <div class="pass-data">
                        <input type="checkbox" id="check" class="form-check-input" onclick="showPassword()">
                        <label for="check" class="form-check-label">Show Password</label>
                    </div>
                    <div class="d-flex justify-content-center mt-4 save-btn">
                        <button class="btn" name="save">Save</button>               
                        </div>                   
                    </div>
                    <?php
                        }
                    ?>
                </from>
            </div>
        </div>
    </div>
        <img class="banner-footer" src="assets/images/wave1.png">
    </section>

    <footer class="footer">
        <!-- start footer-top area -->
        <section class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="content">
                            <div class="title">
                                <h5>Get in Touch</h5>
                            </div>
                            <p class="desc">Get in touch with us to get more about our services or share your feedback.</p>
                            <ul class="address">
                                <li class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <p>24/A New California</p>
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="fas fa-phone-alt"></i>
                                    <p>+1 800 123 4567</p>
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="far fa-envelope"></i>
                                    <p>demo@example.com</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="content">
                            <div class="title">
                                <h5>quick links</h5>
                            </div>
                            <ul class="navigation">
                                <li><a href="#banner">home</a></li>
                                <li><a href="#mail">mail</a></li>
                                <li><a href="profile.php">profile</a></li>
                                <li><a href="profile_update.php">update profile</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="content">
                            <div class="title">
                                <h5>follow us</h5>
                            </div>
                            <ul class="follow">
                                <li><a href="#!">facebook</a></li>
                                <li><a href="#!">twitter</a></li>
                                <li><a href="#!">google</a></li>
                                <li><a href="#!">youtube</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="content">
                            <div class="title">
                                <h5>subscribe</h5>
                            </div>
                            <p>Subscribe to stay up-to-date on the latest news, and insights from our company.</p>
                            <div class="footer-form-area">
                                <input type="email" placeholder="your email" class="inputs">
                                <button><i class="fab fa-telegram-plane"></i></button>
                            </div>
                            <ul class="d-flex social">
                                <li><a href="#!"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#!"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#!"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="#!"><i class="fab fa-linkedin-in"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end footer-top area -->

        <!-- start footer-bottom area -->
        <section class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="bg">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <p>Copyright &copy; 2023 All Right Reserved</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end footer-bottom area -->
    </footer>
    <!-- end footer area -->

    <!-- Adding readonly attribute to email field  -->
    <?php
        // if($status == 1){
            ?>
                <!-- <script>
                    const mail = document.getElementById('email');
                    mail.readOnly = true;
                </script> -->
            <?php
        // }
    ?>

    <script>
    // <!-- prevents form resubmission -->
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

    //confirm logout
    const confirmLogout = () => {
            const confirmLogout = confirm("Are you sure! you want to logout?");
            if(confirmLogout){
                window.location = "logout.php";
            }
        }

    //Displaying password 
    function showPassword(){
        var newPassword = document.getElementById("newPassword");
        var confirmPassword = document.getElementById("confirmPassword");
        if (newPassword.type == 'password'){
            newPassword.type = 'text';
            confirmPassword.type = 'text';
        }else{
            newPassword.type = 'password';
            confirmPassword.type = 'password';
        }
    }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/9d09e1757b.js" crossorigin="anonymous"></script>
</body>
</html>
