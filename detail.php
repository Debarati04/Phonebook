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

        <?php
            if (isset($_GET['id'])){
                $id=$_GET['id'];

                // xss prevention
                $id = htmlspecialchars($id);

                require_once('config.php');

                $errored= false;
                $conn=mysqli_connect(dbhost,dbuser,dbpass, db);
                if (!$conn){
                    $errored = TRUE;
                    $errorMsgGlobal = 'Something went wrong. Please try again later';
                } else {
                
                    $sql = "SELECT id, name, phone, image FROM Contacts";
                    $retval = mysqli_query($conn, $sql);
                    if ($retval){
                        //success
                    } else {
                        $errored = TRUE;
                        $errorMsgGlobal = "Something went wrong. Please try again later";
                    }
                    
                }
            }
        ?>

        <div class="title">
            <a href="index.php">Phonebook</a>
        </div>

        <?php
            // sql injection prevention - real escape
            $id = mysqli_real_escape_string($conn, $id);

            $sql = "SELECT id, name, phone, image, email, address, birthday FROM Contacts where id='$id' AND username='$logged_in_user'";
            $retval = mysqli_query($conn, $sql);

            $row=mysqli_fetch_array($retval);
            
            if (mysqli_num_rows($retval) < 1) {
                $errored = TRUE;
                $errorMsgGlobal = "Record doesn't exist."; 
            }

            if($errored) {
        ?>
            <div class="msg error-msg">
                <?php echo $errorMsgGlobal; ?>
            </div>
        <?php
            } else {
        ?>
        <div class="detail">
            <?php 
                if ($row['image'] =='') {
                    $row['image'] = "emptyPic.png"; 
                }
            ?>
            <div class="detail-image" style="background-image: url('uploads/<?php echo $row['image'] ?>')">
            </div>
            <div class="info">
                <div class="detail-name"><?php echo $row['name']; ?></div>
                <ul>
                    <?php if(!empty($row['phone'])) { ?>
                    <li>&#128222; <?php echo $row['phone']; ?></li>
                    <?php } ?>

                    <?php if(!empty($row['email'])) { ?>
                    <li>&#128231; <?php echo $row['email']; ?></li>
                    <?php } ?>

                    <?php if(!empty($row['address'])) { ?>
                    <li>&#127968; <?php echo $row['address']; ?></li>
                    <?php } ?>

                    <?php if(!empty($row['birthday']) && $row['birthday']!= '0000-00-00' ) { ?>
                    <li>&#127874; <?php echo $row['birthday']; ?></li>
                    <?php } ?>
                </ul>
            </div>  
        </div>
        <div class="options">
            <a class="button" href="addcontact.php?id=<?php echo $id;?>">Edit</a>
            <a class="button" id="delete_contact" data-id="<?php echo $id; ?>">Delete</a>
            <script type="text/javascript" src="delete.js"></script>
        </div>
        <?php
            }
        ?>     
        <a class="button" href="index.php">Home</a>
    </body>
</html>