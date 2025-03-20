<?php
    require('functions.php');
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../style1.css">
</head>
<body>
<?php include('adminnavbar.php'); ?>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Registered Users</h5><hr>
    <p class="card-text">No.of Total Users :<?php echo get_user_count();?></p>
    <a href="registeruser.php" class="btn-primary">View Users</a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Registered Books</h5><hr>
    <p class="card-text">No.of Total Books :<?php echo get_book_count();?></p>
    <a href="registerbooks.php" class="btn-primary" style="background-color: rgb(134, 39, 207);">View Books</a>
  </div>
</div>



<div class="card">
  <div class="card-body">
    <h5 class="card-title">Registered Authors</h5><hr>
    <p class="card-text">No.of Total Authors :<?php echo get_author_count();?></p>
    <a href="registerauthors.php" class="btn-primary" style="background-color: rgb(235, 46, 46);">View Authors</a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Issued Books</h5><hr>
    <p class="card-text">No.of Issued Books :<?php echo get_issue_count();?></p>
    <a href="registerissue.php" class="btn-primary" style="background-color: rgb(50, 46, 156);">View Issues</a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Request Books</h5><hr>
    <p class="card-text">No.of Request Books :<?php echo get_request_count();?></p>
    <a href="bookrequest.php" class="btn-primary" style="background-color: rgb(50, 46, 156);">View Requested Book</a>
  </div>
</div>


</body>
</html>