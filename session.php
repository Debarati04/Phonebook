  <?php
    session_start();
    if (!isset($_SESSION['username'])){
        header("location: login.php");
    } else {
        $logged_in_user=$_SESSION['username'];
    }
?>