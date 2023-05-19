<?php
    @include 'db.php';
    
    session_start();
    
    if(!$_SESSION['logged']){
        header('location : login.php');
    }
    
    // Create connection
    $con = mysqli_connect($servername, $username, $password, $database);


    if(isset($_SESSION['email'])){
        $email = $_SESSION['email'];
        $sql = "SELECT * FROM `user` WHERE email ='$email'";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        $name = $row['name'];
        $phone = $row['phone'];
        $password = $row['password'];
        $noChangeError = $nameError = $numError = $phoneError = $emailError = '';
    }

    if(isset($_POST['update'])){
        $updated_name = $_POST['name'];
        $updated_email = $_POST['email'];
        $updated_phone = $_POST['phone'];
        $updated_password = $_POST['password'];

        //No modification validation
        if($updated_name == $name && $updated_email == $email && $updated_phone == $phone ){
            $noChangeError = "No Changes Made!";
        }else{
            $noChangeError = "";
        }

        //Name field validation
        if($updated_name == ''){
            $nameError = "Name is required!";
        }else{
            $nameError = "";
        }

        //Phone field validation
        if($updated_phone == ''){
            $phoneError = "Phone number is required!";
        }else{
            $phoneError = "";
        }

        //Email field validation
        if($updated_email == ''){
            $emailError = "Email address is required!";
        }else if(!filter_var($updated_email, FILTER_VALIDATE_EMAIL)){
            $emailError = "Invalid Email address!";
        }else if($updated_email != $email){
            $sql3 = "SELECT * FROM user WHERE email = '$updated_email'";
            $result3 = mysqli_query($con, $sql3);
            $num = mysqli_num_rows($result3);
            if($num > 0){
                $emailError = "Email address already exist!";
            }else{    
                $emailError = "";
            }
        }else{    
            $emailError = "";
        }

        //Phone number field validation
        if(strlen($updated_phone)>0 && strlen($updated_phone)<10 || strlen($updated_phone)>10){
            $numError = "Please enter 10 digit phone number!";
        }else{
            $numError = "";
        }
        
        //Current field value updation
        if($nameError != '' || $numError != '' || $phoneError != '' || $emailError != ''){
            $name = $updated_name;
            $email = $updated_email;
            $phone = $updated_phone;
        }

        //db query execution 
        if($nameError == '' && $numError == '' && $phoneError == '' && $emailError == '' && $noChangeError == ''){

            $sql2 = "UPDATE user SET email='$updated_email', name='$updated_name', phone='$updated_phone', password='$updated_password' WHERE email='$email'";

            $result2 = mysqli_query($con, $sql2);

            if($result2){
                $_SESSION['email'] = $updated_email;
                $_SESSION['status'] = true;
                header('location: profile.php');
            }else{
                $_SESSION['email'] = $email;
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
    <title>Update</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
    <!-- font-family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">

    <style>
            input:-webkit-autofill,
            input:-webkit-autofill:hover,
            input:-webkit-autofill:focus,
            input:-webkit-autofill:active {
                -webkit-text-fill-color: var(--light-gray);
                transition: background-color 5000s ease-in-out 0s;
            }
    </style>
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
            <li><a class="dropdown-item" href="password_update.php"  name="password">Password</a></li>
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

<!-- Form area -->
<section class="update-area">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-10 update-profile-data-area">
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="card-block profile-icon">
                                <i class="fa fa-user fa-7x mt-5"></i>
                                <!-- <i class="fa-solid fa-user"></i> -->
                            </div>
                        </div>
                        <div class="col-md-8 user-data-area">
                            <form action="#" method="POST">
                                <span id="error" class="d-flex justify-content-center noChangeError"><?php echo $noChangeError?></span>
                                <div class="user-data-group">
                                    <div class="user-data">
                                        <label for="name" class="form-label">Name<span id="error">*</span> : </label>
                                        <input type="text" class="form-control " name="name" value="<?php echo $name ?>">
                                    </div>
                                    <span id="error"><?php echo $nameError?></span>
                                </div>
                                <div class="user-data-group">
                                    <div class="user-data">
                                        <label for="email" class="form-label">Email<span id="error">*</span> : </label>
                                        <input type="text" class="form-control " name="email" value="<?php echo $email ?>">
                                    </div>
                                    <span id="error"><?php echo $emailError?></span>
                                </div>
                                <div class="user-data-group">
                                    <div class="user-data">
                                        <label for="phone" class="form-label">Phone<span id="error">*</span> : </label>
                                        <input type="text" class="form-control " name="phone" value="<?php echo $phone ?>">
                                    </div>
                                    <span id="error"><?php echo $phoneError?></span>
                                    <span id="error"><?php echo $numError?></span>
                                </div>
                                <div class="user-data-group">
                                    <div class="user-data">
                                        <label for="password" class="form-label">Password<span id="error">*</span> : </label>
                                        <input type="text" class="form-control" name="password" readonly value="<?php echo $password ?>">
                                    </div>
                                </div>
                                <div class="d-flex update-btn">
                                    <button class="btn mb-2 " name="update">Update</button>               
                                    </div>                   
                                </div>
                            </from>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <img class="banner-footer" src="assets/images/wave1.png">
</section>
    <!-- Form area end-->

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

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/9d09e1757b.js" crossorigin="anonymous"></script>
</body>
</html>
