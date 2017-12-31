<?php
    require_once('session.php');
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>PhoneBook Contacts Display</title>
        <link rel="stylesheet" href="ContactDisplay.css" type="text/css"/>
    </head>
    <body>
        <?php
            require_once('config.php');

            $errored= false;
            $conn=mysqli_connect(dbhost,dbuser,dbpass, db);
            if (!$conn){
                $errored = TRUE;
                $errorMsgGlobal = 'Something went wrong. Please try again later';
            } else {
            
                $sql = "SELECT id, name, phone, image FROM Contacts WHERE username = '$logged_in_user'";
                $retval = mysqli_query($conn, $sql);
                if ($retval){
                    //success
                } else {
                    $errored = TRUE;
                    $errorMsgGlobal = "Something went wrong. Please try again later";
                }
                
            }

        ?>

        <div class="title">
            <a href="index.php">Phonebook</a>
        </div>

        <div class="content">

            <div class="no-records"><?php echo $_SESSION['username'];?></div>
        <?php
            if($errored) {
        ?>
            <div class="msg error-msg">
                <?php echo $errorMsgGlobal; ?>
            </div>
        <?php
            } else {
                if (mysqli_num_rows($retval) > 0) {
                    while ($row = mysqli_fetch_array($retval)){ 
        ?>
                        <a href="detail.php?id=<?php echo $row['id']; ?>">
                            <div class="contact-card">
                                <div class="contact-icon">
                                <?php
                                    $image = $row['image'];
                                    if (!isset($image) || $image == '') {
                                        $image = 'emptyPic.png';
                                    }
                                ?>
                                    <div class="contact-image" style="background-image: url('uploads/<?php echo $image; ?>')">
                                    </div>
                                </div>
                                <div class="contact-detail">
                                    <div><?php echo $row['name']; ?></div>
                                    <div><?php echo $row['phone']; ?></div>
                                </div>
                            </div>
                        </a>
            <?php
                    }
                } else {
            ?>
                <div class="no-records">
                    No records found
                </div>
            <?php
                }
            }
            if($conn) {
                mysqli_close($conn);
            }
        ?>
        </div>
        <a  class="button" href="sign-out.php">Sign Out</a>
        <a class="button addContact" href="addcontact.php">Add Contact</a>
    </body>
</html>