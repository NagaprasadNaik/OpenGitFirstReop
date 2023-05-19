<?php

    @include 'db.php';

    session_start();

    if(!$_SESSION['logged']){
        header('location : login.php');
    }

    // Create connection
    $con = mysqli_connect($servername, $username, $password, $database);

    // Check connection
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }


    //checking request parameter is set or not.
    if(isset($_GET['email'])){
        $_SESSION['email'] = $_GET['email'];
    }

    $email = $_SESSION['email'];
    $sql = "SELECT * FROM `user` WHERE email ='$email'";
    $result = mysqli_query($con, $sql);    
    $row = mysqli_fetch_assoc($result);

    if($_SESSION['status']){
    ?>
        <script>
           window.addEventListener('load', function(){
            swal({
                title: "Updated Successfully!",
                icon: "success",
                button: "Ok",
            });
           })
        </script>
    <?php
    $_SESSION['status'] = false;
    }

    //email feature
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;  

    function sendMail($to,$sub,$mes,$email){

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
            $mail->addAddress($to);               
            $mail->addReplyTo($email);
        
            //Content
            $mail->isHTML(true);                                  
            $mail->Subject = $sub;
            $mail->Body    = $mes;
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    $subject = $message = $flag = "";
    $email_err = $subject_err = $message_err ="";
    $email_value = $subject_value = $message_value = "";

    function testinput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    if(isset($_POST['send'])){
        $to=$_POST['email'];
        $subject=$_POST['subject'];
        $message=$_POST['message'];
        
        //email validation
        if(empty($to)){
        $subject_value = $subject;
        $message_value = $message ;
        $flag = false;
        echo '<script>alert("Please specify at least one recipieint")</script>';
        }else if(empty($subject) && empty($message)){
            // $message_err = "Message cannot be empty";
            $email_value = $to;
            $subject_value = $subject;
            $flag = false;
            echo '<script>alert("Please note that the message you are about to send has no subject or content.")</script>'; 
        }else if(empty($subject)){
            $email_value = $to;
            $message_value = $message;
            $subject_err = "Subject cannot be empty!";
        }else if(empty($message)){
            $email_value = $to;
            $subject_value = $subject;
            $message_err = "Message cannot be empty!";
        }else{
            $flag = true;
            $to =testinput($to);
            if(!filter_var($to,FILTER_VALIDATE_EMAIL)){
                // $email_err="Enter valid Email address";
                $subject_value = $subject;
                $message_value = $message;
                echo '<script>alert("Invalid Email")</script>';
                $flag = false;
                }
        }

        if ($flag){
        $sub =$subject;
        $mes = $message;
            if(sendMail($to,$sub,$mes,$email)){
                ?>   
                <script>
                    window.addEventListener('load', function(){
                    swal({
                        type:"success",
                        title: "Email Sent Successfully",
                    })
                    });
                </script>
                <?php
            }else{
                ?>
                <script>
                    window.addEventListener('load', function(){
                    swal({
                        type:"warning",
                        title: "Cannot Send!",
                    })
                    });
                </script>
                <?php
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
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
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
    <ul class="navbar-nav ml-auto ">
        <li class="nav-item">
            <a href="#mail" class="nav-link active ms-2 me-2 fw-bold">Mail</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link active fw-bold" id="update" href="#" aria-expanded="false">
            Update
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="profile_update.php"  name="profile">Profile</a></li>
            <li><a class="dropdown-item" href="password_update.php"  name="password">Password</a></li>
          </ul>
          <a href="admin.php" id="admin" class="nav-link active fw-bold">Admin</a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link active me-2 fw-bold" role="button" onclick="confirmLogout()">Logout</a>
        </li>
    </ul>
    </div>
    </nav>
</section>
<!-- navbar End -->

<!-- Information Start-->
<section id="banner">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="name">
                    <h1 class="info">Hi <?php echo $row['name']?></h1>
                    <p class="info"><span class="welcome">Welcome!</span> Our mission is to provide accessible and engaging learning resources for students with a focus on promoting critical thinking, creativity, and lifelong learning.</p>
                </div>
                <div class="button">
                    <a href="#subscribe"><button class="btn">Subscribe</button></a>
                </div>
            </div>
            <div class="col-md-5">
                <img id="profile-img" class="img-fluid rounded-4" src="assets/images/profile2.jpg" alt="profile">
            </div>            
        </div>
        
    </div>
    <img class="banner-footer" src="assets/images/wave1.png">
</section>
<!-- Information End-->

<!-- start feature area -->
<section id="features">
        <div class="container feature-row">
            <div class="row">
                <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2">
                    <div class="feature-title" data-wow-delay="0.3s" data-wow-duration="1s">
                        <h2>apps features</h2>
                        <p>Our app offers a range of innovative features to enhance your experience and simplify your life.</p>
                    </div>
                </div>
                <div class="col-lg-12 mt-3 mb-3">
                    <div class="row justify-content-center grid gap-3">
                        <div class="card shadow">
                            <img src="assets/images/portfolio.png" class="card-img-top" alt="img">
                            <div class="card-body">
                                <h5 class="card-title">Interactive portfolio</h5>
                                <p class="card-text">Our app allows users to showcase their work or accomplishments in an interactive way, such as through a gallery or slideshow.</p>
                                <a href="#" class="arrow"><i class="fa-solid fa-arrow-right-long"></i></a>
                            </div>
                        </div>
                        <div class="card shadow">
                            <img src="assets/images/social-media.png" class="card-img-top" alt="img">
                            <div class="card-body">
                                <h5 class="card-title">Social media integration</h5>
                                <p class="card-text">Our app allows users to connect their social media accounts to their profile website and display their latest posts or activity.</p>
                                <a href="#" class="arrow"><i class="fa-solid fa-arrow-right-long"></i></a>
                            </div>
                        </div>
                        <div class="card shadow">
                            <img src="assets/images/templates.png" class="card-img-top" alt="img">
                            <div class="card-body">
                                <h5 class="card-title">Customizable templates</h5>
                                <p class="card-text">Our app allows users to choose from a variety of pre-designed templates and customize them to fit their personal brand.</p>
                                <a href="#" class="arrow"><i class="fa-solid fa-arrow-right-long"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end feature area -->

    <!-- start newsletter area -->
    <section id="subscribe">
        <div class="container">
            <div class="newsletter">
                    <div class="mail-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="sub-title">
                        <h3>Subscribe for Newsletter</h3>
                        <p>Stay up-to-date and informed with our newsletter.</p>
                    </div>
                    <div class="form-area">
                        <input type="email" placeholder="Enter your email" class="inputs">
                        <a href="#banner"><button type="submit" class="button-style">subscribe</button></a>
                    </div>
            </div>
        </div>
    </section>
    <!-- end newsletter area -->

    <!-- mail feature area -->
    <section id="mail">
        <div class="row mail-row">
            <div class="container col-md-3 mail">
                <form class="" method="post" action="#">
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="<?php echo $email_value; ?>">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Subject" id="Subject" name="subject" value="<?php echo $subject_value; ?>">
                        <span class="text-danger"> <?php echo $subject_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" rows="5" placeholder="message" name="message"><?php echo $message_value; ?></textarea>
                        <span class="text-danger"> <?php echo $message_err; ?></span>
                    </div >
                    <div class="text-center">
                        <button class="btn btn-primary btn-lg px-5 " type="submit" name="send">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- mail feature area end-->

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
                                <li><a href="profile_update.php">update profile</a></li>
                                <li><a href="password_update.php">update password</a></li>
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

    <!-- displaying admin in navbar -->
    <?php
    if(isset($_GET['role'])){
        if($_GET['role'] == 'admin'){
        ?>
            <script>
                    p = document.getElementById('mail'); 
                    p.setAttribute('class',"d-none");
                    m = document.getElementById('update'); 
                    m.setAttribute('class',"d-none");
            </script>
        <?php
        }           
    }else{
        ?>
            <script>
                    a = document.getElementById('admin'); 
                    a.setAttribute('class',"d-none");
            </script>
        <?php
    }
    ?>

    <!-- prevents form resubmission -->
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }

        const confirmLogout = () => {
            const confirmLogout = confirm("Are you sure! you want to logout?");
            if(confirmLogout){
                window.location = "logout.php";
            }
        }

    </script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/9d09e1757b.js" crossorigin="anonymous"></script>

</body>
</html>