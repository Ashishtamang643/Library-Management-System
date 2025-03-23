<?php
     require('functions.php');
     session_start();
     if (!isset($_SESSION['Name'])) {
        echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
        exit();
    }
     $connection = mysqli_connect("localhost","root","");
     $db = mysqli_select_db($connection,"library");
     $category = "";
     $query = "select distinct category from books";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <link rel="stylesheet" href="../style2.css">
</head>
<body>
<?php include('adminnavbar.php'); ?>

    <h2 class="h2-register-header">Registered Category</h2>
    <table>
        <thead>
          <tr>
             <th>Category</th>
          </tr>
        </thead>
        <?php
             $query_run = mysqli_query($connection,$query);
             while($row = mysqli_fetch_assoc($query_run))
             {
                $category = $row['category'];
                ?>
        <tr>
            <td><?php echo $category;?></td>
        </tr>
        <?php
             }
            ?>
    </table>
</body>
</html>