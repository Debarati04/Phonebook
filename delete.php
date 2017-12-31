<?php
    require_once('session.php');
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>detail</title>
        <link rel="stylesheet" href="ContactDisplay.css" type="text/css"/>
    </head>
    <body>
        <div class="title">
            <a href="index.php">Phonebook</a>
        </div>

        <?php
            if (isset($_GET['id'])){
                $id=$_GET['id'];

                // xss prevention
                $id = htmlspecialchars($id);

                require_once('config.php');

                $errored= false;
                $conn=mysqli_connect(dbhost, dbuser, dbpass, db);
                if (!$conn){
                    $delete_status = false;
                    $errored = TRUE;
                    $errorMsgGlobal = 'Something went wrong. Please try again later';
                } else {
                    $sql = "DELETE FROM Contacts WHERE id='$id' AND username='$logged_in_user'";
                    $retval = mysqli_query($conn, $sql);
                    if ($retval){
                        //success
                        $delete_status = true;
                    } else {
                        $delete_status = false;
                        $errored = TRUE;
                        $errorMsgGlobal = "Something went wrong. Please try again later";
                    }
                    
                }
            }
            if ($delete_status == true){
        ?>
        <div class="delete">Contact deleted successfully</div>
        
        <?php 
            } else { ?>
                <div class="delete">Could not delete contact</div>
         <?php   }
        ?>
        <div class="options">
            <a class="button addContact" href="addcontact.php">Add Contact</a>
            <a class="button" href="index.php">Home</a>
        </div>
    </body>
</html>