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
    <style>
        #error{ color:red;}
        .navbar-dark .navbar-nav  .nav-link:hover{color: orange;}
    </style>
</head>
<body class="bg-light">
    <!-- navbar start -->
    <nav class="navbar navbar-expand navbar-dark bg-dark pt-4 pb-4">
    <div class="container">
    <div class="logo">
        <a href="profile.php" ><img id="main-logo" src="assets/images/PROFILE-removebg-preview (1).png" /></a>
    </div>
    <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link active ms-2 me-2 fw-bold " href="profile.php" role="button">
            Profile
          </a>
        </li>
        <li class="nav-item">
            <a href="mail.php" class="nav-link active ms-2 me-2 fw-bold" >Mail</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link active ms-2 me-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modal">Logout</a>
            <div class="modal fade" id="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                           <p class="fw-bold">Confirm Logout</p> 
                        </div>
                        <div class="modal-body mt-3 mb-3 ps-5 ">
                           <p>Are you sure! you want to logout?</p> 
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" data-bs-dismiss="modal" data-bs-target="#modal">No</button> 
                           <a href="logout.php"><button class="btn btn-success">Yes</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    </div>
    </nav>
    <!-- navbar End -->

    <!-- Form area -->
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6 mt-5 pt-5 border border-2 rounded-3 bg-white">
                <form action="#" method="POST">
                    <span id="error" class="d-flex justify-content-center"><?php echo $noChangeError?></span>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name<span id="error">*</span> : </label>
                        <input type="text" class="form-control" name="name" value="<?php echo $name ?>">
                        <span id="error"><?php echo $nameError?></span>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email<span id="error">*</span> : </label>
                        <input type="text" class="form-control" name="email" value="<?php echo $email ?>">
                        <span id="error"><?php echo $emailError?></span>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone<span id="error">*</span> : </label>
                        <input type="text" class="form-control" name="phone" value="<?php echo $phone ?>">
                        <span id="error"><?php echo $phoneError?></span>
                        <span id="error"><?php echo $numError?></span>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password<span id="error">*</span> : </label>
                        <input type="text" class="form-control" name="password" readonly value="<?php echo $password ?>">
                    </div>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-primary mb-2 " name="update">Update</button>               
                        </div>                   
                    </div>
                </from>
            </div>  
        </div>
    </div>
    <!-- Form area end-->

    <!-- prevents form resubmission -->
    <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>
</body>
</html>
