<?php
    require_once('session.php');
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    session_destroy();
    header("location: login.php");
?>