<?php

    session_start();
    if (isset($_SESSION['username'])){
        header("location:index.php");
    }
    require_once('config.php');

    if (isset($_POST['submit'])){
        $errored = FALSE;
        $error['uname']=NULL;
        $error['pswd']=NULL;
        $errorMsgGlobal = 'Please correct the error below.';

        //username validation
        if (empty($_POST['uname'])){
            $errored=TRUE;
            $error['uname']="Please enter username";
        }
        $uname=htmlspecialchars($_POST['uname']);

        if(empty($_POST['pswd'])){
            $errored=TRUE;
            $error['pswd']="Please enter password";
        }
        $pswd=htmlspecialchars($_POST['pswd']);

        if ($errored == FALSE){
            //pushing into userinfo table
            $errored= false;
            $conn=mysqli_connect(dbhost,dbuser,dbpass, db);
            if (!$conn){
                $errored = TRUE;
                $errorMsgGlobal = 'Something went wrong. Please try again later';
            } else{

                //not in database 'invalid combination'
                $sql="SELECT username, password FROM UserInfo WHERE username ='$uname' AND password = '$pswd'";
                $retval=mysqli_query($conn,$sql);
                if ($retval){
                    if (mysqli_num_rows($retval) > 0){
                        session_start();
                        $_SESSION['username']=$uname;
                        header("location: index.php");
                    } else {
                        $errored = TRUE;
                        $errorMsgGlobal = 'Invalid username and password combination';
                    }
                } else{
                    $errored = TRUE;
                    $errorMsgGlobal = 'Something went wrong. Please try again later';
                }
            }
            mysqli_close($conn);
        }
    }
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Page</title>
        <link rel="stylesheet" href="ContactDisplay.css" type="text/css"/>
    </head>
    <body>
        <div class="title">
            <a href="login.php">Phonebook Login</a>
        </div>

        <div class="content">
            <?php
                if($errored) {
            ?>
            <div class="msg error-msg">
                <?php echo $errorMsgGlobal; ?>
            </div>
            <?php 
                }
            ?>
            <form method="post">
                <div class="forminput">
                    <label class="input-title">Username</label>
                    <div class="input-validation"><?php echo $error['uname']?></div>
                    <input class ="input-field" type="text" name="uname">
                </div>
                <div class="forminput">
                    <label class="input-title">Password</label>
                    <div class="input-validation"><?php echo $error['pswd']?></div>
                    <input class="input-field" type="password" name="pswd">
                </div>
                <input class="input-field" type="submit" name="submit" value="Sign In">
            </form>
            <!-- SIGN UP href to sign up page-->
            <a class="button" href="sign-up.php">Sign Up</a>
        </div>
    </body>
</html>