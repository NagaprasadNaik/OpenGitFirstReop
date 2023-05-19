<?php

    @include('db.php');

    $con = mysqli_connect($servername, $username , $password, $database);
    $email = $_GET['email'];
    if($email){
        $sql = "UPDATE user SET mail_status = 1 WHERE email = '$email' ";
        $result = mysqli_query($con, $sql);
        if($result == true){
            ?>
                <script>
                    alert('Email verified successfully. Please Login!');
                    window.location = "login.php";
                </script>
            <?php
        }
    }else{
        ?>
        <script>
            alert('Invaild Email!. Please verify your email address!');
            window.location = "login.php";
        </script>
    <?php
    }
?>