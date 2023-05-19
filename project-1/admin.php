<?php
    @include 'db.php';

    // if(!$_SESSION['logged']){
    //     header('location : login.php');
    // }

    session_start();
    
    $role = $_SESSION['role'];

    $con = mysqli_connect($servername, $username, $password, $database);
    
    //reqired values for pagination 
    $limit = 7;
    if(isset($_GET['id'])){
        $page = $_GET['id'];
    }else{
        $page = 1;
    }
    $offset = ($page - 1) * $limit;
    //

    if($_SESSION['flag']){
        echo "<script>alert('Record Deleted Successfully!')</script>";
        $_SESSION['flag'] = false;
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <style>
        #error{ color:red;}
        .navbar-dark .navbar-nav  .nav-link:hover{color: orange;}
    </style>
</head>
<body class="bg-white">
    <!-- navbar start -->
    <nav class="navbar navbar-expand navbar-dark bg-dark pt-4 pb-4">
    <div class="container">
    <a href="#" class="navbar-brand fw-bold fs-4 border border-1 rounded p-1">Admin</a>
    <ul class="navbar-nav">
        <li class="nav-item">
            <a href="#" class="nav-link active ms-2 me-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modal">Logout</a>
            <div class="modal fade" id="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                           <p class="fw-bold">Confirm Logout</p> 
                        </div>
                        <div class="modal-body mt-2 mb-2 ps-5 ">
                           <p class="fs-5">Are you sure! you want to logout?</p> 
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

    <!-- Table area start -->

    <div class="container mt-5">
        <table class="table table-bordered table-striped table-hover bg-light">
            <thead>
                <tr class="table-dark">
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
        <tbody>
            <?php
                $sql = "SELECT * FROM user WHERE role = 'user' LIMIT $offset, $limit";
                $result = mysqli_query($con, $sql);
                
                if(mysqli_num_rows($result) > 0){
                    foreach($result as $data){
                        ?> 
                            <tr>
                                <td scope="row"><?= $data['uid'] ?></td>
                                <td scope="row"><?= $data['name'] ?></td>
                                <td scope="row"><?= $data['phone'] ?></td>
                                <td scope="row"><?= $data['email'] ?></td>
                                <td scope="row"><?= $data['status'] ?></td>
                                <td>
                                    <a href="profile.php?email=<?= $data['email'] ?>&role=<?= $role?>" class="btn btn-primary">View</a>
                                    <a href="admin_edit.php?id=<?= $data['uid'] ?>&role=<?= $role?>" class="btn btn-success">Edit</a>
                                    <a href="admin_delete.php?id=<?= $data['uid'] ?>" onclick="return confirmation()" class="btn btn-danger">Delete</a>
                                </td>
                            </tr> 
                        <?php
                    }
                }else{
                    echo "<script>alert('No records found!')</script>";
                }
            ?>
        </tbody>
    </table>    

    <!-- Pagination area start -->
    <?php

        $sql2 = "SELECT * FROM user WHERE role = 'user'";
        $result2 = mysqli_query($con, $sql2);

        if(mysqli_num_rows($result2) > 0){
            $total_records = mysqli_num_rows($result2);
            //$limit is number of records displayed on a single page 
            //$total_pages is number of pages reqiured to display all records
            $total_pages = ceil($total_records/$limit);
            
            echo '<ul class="pagination d-flex justify-content-center fixed-bottom">';

            //Previous button 
            if($page > 1){
                $disabledPrev = "";
            }else{
                $disabledPrev = "disabled";
            }
            echo '<li class="page-item '.$disabledPrev.'"><a href="admin.php?id='.($page - 1).'" class="page-link">Prev</a></li>';
            
            //total number of pages 
            for($i=1; $i<=$total_pages; $i++){
                if($i == $page){
                    $active = 'active';
                }else{
                    $active = '';
                }
                echo '<li class="page-item '.$active.'"><a href="admin.php?id='.$i.'" class="page-link">'.$i.'</a></li>';
            }

            //Next button
            if($page < $total_pages){
                $disabledNext = "";
            }else{
                $disabledNext = "disabled";
            }
            echo '<li class="page-item '.$disabledNext.'"><a href="admin.php?id='.($page + 1).'" class="page-link">Next</a></li>';
            
            echo '</ul>';
        }
    ?>
    <!-- Pagination area end -->
</div>
<!-- Table area end -->

    <script>
        function confirmation(){
            return confirm("Are you sure! you want to delete this record?");
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N"
    crossorigin="anonymous"></script>
</body>
</html>