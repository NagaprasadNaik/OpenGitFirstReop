<?php
    @include 'db.php';
    session_start();

    $con = mysqli_connect($servername, $username, $password, $database);
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $sql = "DELETE FROM user WHERE uid = '$id' ";
        $result = mysqli_query($con, $sql);
        
        if($result){
            $_SESSION['flag'] = true;
        }else{
            $_SESSION['flag'] = false;
        }

        header('location: admin.php');
    }
?>



