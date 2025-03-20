<?php
     require('functions.php');
     session_start();
     $connection = mysqli_connect("localhost","root","");
     $db = mysqli_select_db($connection,"library");
     $id="";
     $name="";
     $email="";
     $username="";
     $address="";
     $cell="";
     $query = "select * from users";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <link rel="stylesheet" href="../style1.css">
</head>
<body>
<?php include('adminnavbar.php'); ?>
   

    <h2 class="h2-register-header" >Registered Users</h2>
    <table>
        <thead>
          <tr>
             <th>ID</th>
             <th>Name</th>
             <th>Email</th>
             <th>Faculty</th>
             <th>Address</th>
             <th>Cell</th>
          </tr>
        </thead>
        <?php
             $query_run = mysqli_query($connection,$query);
             while($row = mysqli_fetch_assoc($query_run))
             {
                $id = $row['ID'];
                $name = $row['Name'];
                $email = $row['Email'];
                $faculty = $row['faculty'];
                $address = $row['Address'];
                $cell = $row['Cell'];
                ?>
        <tr>
            <td><?php echo $id;?></td>
            <td><?php echo $name;?></td>
            <td><?php echo $email;?></td>
            <td><?php echo $faculty;?></td>
            <td><?php echo $address;?></td>
            <td><?php echo $cell;?></td>
        </tr>
        <?php
             }
            ?>
    </table>
</body>
</html>