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
        $error['retypedpswd']=NULL;
        $errorMsgGlobal = 'Please correct the error below.';
    
        //username validation
        if (empty($_POST['uname'])){
            $errored=TRUE;
            $error['uname']="Please enter username";
        }
        $uname=htmlspecialchars($_POST['uname']);

        //password validation
        if(empty($_POST['pswd'])){
            $errored=TRUE;
            $error['pswd']="Please enter password";
        }
        $pswd=htmlspecialchars($_POST['pswd']);

        //retyped password validation
        if(!empty($_POST['retypedpswd'])){
            if ($_POST['retypedpswd']!= $_POST['pswd']){
                $errored=TRUE;
                $error['retypedpswd']="Please enter the same password";
            }
        } else {
            $errored=TRUE;
            $error['retypedpswd']="Please retype the password";
        }
        $retypedpswd=htmlspecialchars($_POST['pswd']);
        if ($errored == FALSE){
            //pushing into userinfo table
            $errored= false;
            $conn=mysqli_connect(dbhost,dbuser,dbpass, db);
            if (!$conn){
                $errored = TRUE;
                $errorMsgGlobal = 'Something went wrong. Please try again later';
            } else{
                $sql="INSERT INTO UserInfo (username, password) VALUES ('$uname', '$pswd')";
                $retval=mysqli_query($conn,$sql);
                if ($retval){
                    $success = TRUE;
                    $successMsgGlobal = 'Signed up successfully. Please sign in now';
                }else {
                    $errored = TRUE;
                    $errorMsgGlobal = "Something went wrong. Please try again later";
                }
                mysqli_close($conn);
            }

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
                else if($success){
            ?>
            <div class="msg success-msg">
                <?php echo $successMsgGlobal; ?>
            </div>
            <?php 
                }
            ?>
            <form method="post">
                <div class="forminput">
                    <label class="input-title">Username</label>
                    <div class="input-validation"><?php echo $error['uname']?></div>
                    <input class ="input-field" type="text" name="uname" value="<?php echo $uname; ?>">
                </div>
                <div class="forminput">
                    <label class="input-title">Password</label>
                    <div class="input-validation"><?php echo $error['pswd']?></div>
                    <input class="input-field" type="password" name="pswd">
                </div>
                <div class="forminput">
                    <label class="input-title">Re-type Password</label>
                    <div class="input-validation"><?php echo $error['retypedpswd']?></div>
                    <input class="input-field" type="password" name="retypedpswd">
                </div>
                <input class="input-field" type="submit" name="submit" value="Sign Up">
            </form>
            <!-- SIGN UP href to sign up page-->
            <a class="button" href="login.php">Sign In</a>
        </div>
    </body>
</html>