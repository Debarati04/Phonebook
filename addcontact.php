<?php
    require_once('session.php');
    require_once('config.php');

    if (isset($_POST['submit'])) {

        $errored = FALSE;
        $error['name']=NULL;
        $error['phone']=NULL;
        $error['email']=NULL;
        $error['address']=NULL;
        $error['birthday']=NULL;
        $error['image']=NULL;
        $errorMsgGlobal = 'Please correct the error below.';


        // name validation
        if (empty($_POST['name'])){
            $errored = TRUE;
            $error['name']="Please enter name";
        }
        $name = htmlspecialchars($_POST['name']);
    
        // phone validation
        if (!empty($_POST['phone'])) {
            if (!filter_var($_POST['phone'], FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^[789][0-9]{9}$/")))) {
                $errored = TRUE;
                $error['phone']="Please enter valid 10-digit phone number (starting with 7,8 or 9)";
            } else {
                // success
            }
        } else {
            $errored = TRUE;
            $error['phone']="Please enter phone number";
        }
        $phone = htmlspecialchars($_POST['phone']);
        
        // email validation
        if (!empty($_POST['email'])){
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false){
                $errored = TRUE;
                $error['email']="Please enter valid email id";
            }
        }
        $email = htmlspecialchars($_POST['email']);
        
        // no extra validation for address
        $address = htmlspecialchars($_POST['address']);
        
        // birthday validation
        if (!empty($_POST['birthday'])){
            $matches=array();
            if (preg_match("/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/", $_POST['birthday'], $matches)){
                // echo "$matches[3], $matches[1], $matches[2]";
                if (checkdate($matches[2], $matches[3], $matches[1])){
                    // success
                } else{
                    $errored = TRUE;
                    $error['birthday']="Please enter valid birthday";
                }
            } else {
                $errored = TRUE;
                $error['birthday']="Please enter valid birthday";
            }
        }
        $birthday = htmlspecialchars($_POST['birthday']);
        
        //file validation
        $imageTargetName = ''; //generate a hash value later
        $fileUploadErrored = 0; //check server errors
        if (!isset($_FILES['image']['error']) || is_array($_FILES['image']['error'])){
            // ignore, as if no file
            // undefined, multi error, hack prevention
        } else {
            switch ($_FILES['image']['error']){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $fileUploadErrored = 1;
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errored = TRUE;
                    $fileUploadErrored = 1;
                    $error['image'] = "File upload error. File size too big";
                    break;
                default:
                    $errored = TRUE;
                    $fileUploadErrored = 1;
                    $error['image'] = "File could not be uploaded";
            }

            if ($fileUploadErrored == 0){
                $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $check = getimagesize($_FILES['image']['tmp_name']);
                if ($check != false){
                    if ($imageFileType == "jpeg" || $imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "gif"){
                        if ($_FILES['image']['size'] < 5000000){
                            $imageTargetName = md5(time() . rand(1,10000)) . '.' . $imageFileType;
                            if (move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imageTargetName)){
                                // success
                            } else {
                                $errored = TRUE;
                                $fileUploadErrored = 1;
                                $error['image'] = "Something went wrong. Please try again";
                            }
                        } else {
                            $errored = TRUE;
                            $fileUploadErrored = 1;
                            $error['image'] = "Please upload a file of size less than 5MB";
                        }
                    } else {
                        $errored = TRUE;
                        $fileUploadErrored =1;
                        $error['image'] = "Please upload a valid jpeg or png or gif image";
                    }
                } else {
                    $errored = TRUE;
                    $fileUploadErrored = 1;
                    $error['image'] = "Please upload a valid image";
                }
            }
        }

        if ($errored == FALSE){
            $errored= false;
            $conn=mysqli_connect(dbhost,dbuser,dbpass, db);
            if (!$conn){
                $errored = TRUE;
                echo "rt";
                $errorMsgGlobal = 'Something went wrong. Please try again later';
            } else {

                if (isset($_POST['id'])){
                    echo "po";
                    $id = htmlspecialchars($_POST['id']);
                    $id = mysqli_real_escape_string($conn,$id);
                    if ($imageTargetName != ''){
                        echo "p";
                        $sql = "UPDATE Contacts SET name='$name', phone='$phone', email='$email', address='$address', birthday='$birthday', image='$imageTargetName' WHERE id = ('$id') AND username='$logged_in_user'";
                    } else {
                        echo "u";
                        $sql = "UPDATE Contacts SET name='$name', phone='$phone', email='$email', address='$address', birthday='$birthday' WHERE id = ('$id') AND username='$logged_in_user'";
                    }
                    $edit_mode = 1;
                } else {
                    echo "o";
            
                    $sql = "INSERT INTO Contacts (username, name, phone, email, address, birthday, image) VALUES ('$logged_in_user', '$name', '$phone', '$email', '$address', '$birthday', '$imageTargetName')";
                    $edit_mode = 2;
                }
                echo "kj";
                $retval = mysqli_query($conn, $sql);
                if ($retval){
                    //success
                    if ($edit_mode == 1){
                        $last_query_id = $id;
                    } else {
                        $last_query_id = mysqli_insert_id($conn);
                    }
                    echo "$id";
                    header("location: detail.php?id=$last_query_id");
                } else {
                    $errored = TRUE;
                    echo "gh";
                    $errorMsgGlobal = "Something went wrong. Please try again later";
                }
                mysqli_close($conn);
            }
        }
    }

    elseif (isset($_GET['id'])){
        $errored = FALSE;
        $id = htmlspecialchars($_GET['id']);
        
        $conn=mysqli_connect(dbhost,dbuser,dbpass, db);
        if (!$conn){
            $errored = TRUE;
            $errorMsgGlobal = 'Something went wrong. Please try again later';
        } else {
            $sql = "SELECT id, name, phone, email, address, birthday, image FROM Contacts WHERE id = '$id' AND username='$logged_in_user'";
            $retval = mysqli_query($conn, $sql);

            if ($retval){
                //success
                if (mysqli_num_rows($retval) > 0){
                    $row = mysqli_fetch_array($retval);

                    $name = $row['name'];
                    $phone = $row['phone'];
                    $email = $row['email'];
                    $address = $row['address'];
                    $birthday = $row['birthday'];
                    $id = $row['id'] ;                
                    

                } else {
                    $id = NULL;
                }



            } else {
                $id = NULL;
                $errored = TRUE;
                $errorMsgGlobal = "Something went wrong. Please try again later";
            }

            mysqli_close($conn);
        }

    }
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="ContactDisplay.css" type="text/css"/>
    </head>
    <body>
        
        <div class="title">
            <a href="index.php">Phonebook</a>
        </div>
        <div class="content">
            <?php
                if($errored) {
            ?>
            <div class="error-msg">
                <?php echo $errorMsgGlobal; ?>
            </div>
            <?php 
                }
            ?>
            <form method="POST" action="" enctype="multipart/form-data" id="contact_form">
                <div class="forminput">
                    <label class="input-title">Name</label>
                    <div class="input-validation" id="val_name"><?php echo $error['name']?></div>
                    <input class="input-field" type="text" name="name" value="<?php echo $name; ?>"/>
                </div>
                <div class="forminput"> 
                    <label class="input-title">Phone (10 digit)</label>
                    <div class="input-validation" id="val_phone"><?php echo $error['phone']?></div>
                    <input class="input-field" type="number" name="phone" value="<?php echo $phone; ?>"/>
                </div>
                <div class="forminput">
                    <label class="input-title">Email</label>
                    <div class="input-validation" id="val_email"><?php echo $error['email']?></div>
                    <input class="input-field" type="text" name="email" value="<?php echo $email; ?>"/>
                </div>
                <div class="forminput">
                    <label class="input-title">Address</label>
                    <div class="input-validation"><?php echo $error['address']?></div>
                    <input class="input-field" type="text" name="address" value="<?php echo $address; ?>"/>
                </div>
                <div class="forminput">
                    <label class="input-title">Birthday</label>
                    <div class="input-validation" id="val_birthday"><?php echo $error['birthday']?></div>
                    <input class="input-field" type="date" name="birthday" value="<?php echo $birthday; ?>"/>
                </div>
                <div class="forminput">
                    <label class="input-title">Picture</label>
                    <div class="input-validation"><?php echo $error['image']?></div>
                    <input class="input-field" type="file" name="image" accept="image/*" value=""/>
                </div>
                <div>
                    <input class="input-field" type="submit" name="submit" value="Save Contact" 
                    id="savecontact">

                    <?php if ("$id" != NULL) { ?>
                        <input class="input-field" type="hidden" name="id" value="<?php echo $id;?>">
                    <?php } ?> 
                </div>
            </form>
            <a class="button" href="index.php">Home</a>
        </div>
        <script type="text/javascript" src="addcontact.js"></script>
    </body>
</html>