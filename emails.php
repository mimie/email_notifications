<html>
<head>
<title>List of Emails</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
  
    include 'dbcon.php';
    include 'notification_functions.php';

    $customerSearchForm = displaySearchForm();
    echo $customerSearchForm;
     
    $emailSearchForm = emailSearchForm();
    echo $emailSearchForm;

    
   if(isset($_GET['emailVal'])){
     $emailVal = $_GET['emailVal'];
     $emailVal = htmlspecialchars($emailVal);
     $replaceForm = displayReplaceEmailForm($emailVal);
     echo $replaceForm;

     if($_POST['replace']){
       $newEmail = htmlspecialchars($_POST["newEmail"]);
       $message = replaceEmail($emailVal,$newEmail);
       $message == "successful"? header("Location:emails.php?updated=$newEmail"):$message;
       echo $message;
     }
   }

    $allEmails = displayAllEmails();
    $email = $_POST['email'];
   
    if(isset($_GET['updated'])){
      $newEmail = $_GET['updated'];
      echo "<div><b>Email has been changed to <i>$newEmail</i></b></div><br>";
      echo $allEmails;
    }

    elseif(isset($_GET['emailRemoved'])){
      $emailRemoved = $_GET['emailRemoved'];
      $message = removeAllNotifications($emailRemoved);
      $message == "successful"? header("Location:emails.php?removed=$emailRemoved"):$message;
      
      echo "<div><b>There is an error encountered.<br><i>$email</i> is not successfully removed.</b></div><br>";
    }

    elseif(isset($_GET['removed'])){
      $emailRemoved = $_GET['removed'];
      echo "<b><div><i>$emailRemoved has been successfully removed to all notifications.</i></div></b><br>";
      echo $allEmails;

    }

    elseif($_POST['searchEmail']!='SEARCH' && !isset($_GET['emailVal'])){
      echo $allEmails;
    }

    elseif($email){
      $emailDisplay = getSearchEmail($email);
      echo $emailDisplay;
    }

   elseif($email==NULL){
      echo $allEmails;

   }

  



?>
</body>
</html>
