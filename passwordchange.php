<?php
session_start();
if (isset($_POST['current_pwd']) && isset($_POST['new_pwd'])) {
    $connection = mysqli_connect("localhost", "root", "", "library");
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fetch the hashed password from the database
    $query = "SELECT Password FROM users WHERE ID = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['ID']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hashed_password);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Verify the current password
    if (password_verify($_POST['current_pwd'], $hashed_password)) {
        // Hash the new password
        $new_hashed_password = password_hash($_POST['new_pwd'], PASSWORD_BCRYPT);

        // Update the password in the database
        $update_query = "UPDATE users SET Password = ? WHERE ID = ?";
        $stmt = mysqli_prepare($connection, $update_query);
        mysqli_stmt_bind_param($stmt, "ss", $new_hashed_password, $_SESSION['ID']);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            ?>
            <script type="text/javascript">
                alert("Password Changed Successfully");
                window.location.href = "index.php";
            </script>
            <?php
        } else {
            ?>
            <script type="text/javascript">
                alert("Failed to update password");
                window.location.href = "changepassword.php";
            </script>
            <?php
        }
        mysqli_stmt_close($stmt);
    } else {
        ?>
        <script type="text/javascript">
            alert("Wrong Password");
            window.location.href = "changepassword.php";
        </script>
        <?php
    }

    mysqli_close($connection);
}
?>