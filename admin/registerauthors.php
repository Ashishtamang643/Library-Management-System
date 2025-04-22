<?php
     require('functions.php');
     session_start();
     if (!isset($_SESSION['Name'])) {
        echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
        exit();
    }
     $connection = mysqli_connect("localhost","root","");
     $db = mysqli_select_db($connection,"library");
     $author = "";
     $query = "select distinct author_name from books";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Authors</title>
    <link rel="stylesheet" href="../style2.css">
</head>
<body>
<?php include('adminnavbar.php'); ?>

<div class="main">
<?php include('sidebar.php'); ?>


<div class="container">



    <h2 class="h2-register-header">Registered Authors</h2>
    <table>
        <thead>
          <tr>
             <th>Authors</th>
          </tr>
        </thead>
        <?php
             $query_run = mysqli_query($connection,$query);
             while($row = mysqli_fetch_assoc($query_run))
             {
                $author = $row['author_name'];
                ?>
        <tr>
            <td><?php echo $author;?></td>
        </tr>
        <?php
             }
            ?>
    </table>

    </div>
</div>
</body>
</html>