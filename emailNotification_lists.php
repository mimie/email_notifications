<html>
<head>
<title>Email Notification Lists</title>
<link rel="stylesheet" type="text/css" href="design.css">
</head>
<body>
<?php

    include 'dbcon.php';
    include 'notification_functions.php';

    $searchForm = displaySearchForm();
    echo $searchForm;

    $emailSearchForm = emailSearchForm();
    echo $emailSearchForm;

    $notificationId = $_GET['id'];
   
    $emailForm = displayEmailForm();
    echo $emailForm;

    $allEmails = splitEmails(getAllEmails());
    $customerEmails = getEmailsPerCustomer($allEmails,$notificationId);

    if(isset($_POST['addEmail'])){
    
      $email = $_POST["email"];

      $message = addEmail($notificationId,$email,$customerEmails);
      $message == 'successful' ? header("Location:emailNotification_lists.php?id=$notificationId&added=added"):$message;
      echo $message."<br><br>";

       //echo'<meta http-equiv="refresh" content="3">';

    }

    if(isset($_GET['email'])){

      $email_update = $_GET['email'];
      $editForm = displayEditForm($email_update);
      echo $editForm;

      if($_POST['updateEmail']){
         $newEmail = $_POST['newEmail'];
         $message = updateEmail($customerEmails,$newEmail);
         
         $message == "successful"? header("Location:emailNotification_lists.php?id=$notificationId&updated=updated"):$message;

         echo $message;       
      }
    }

    
      elseif(isset($_GET['emailRemoved'])){
        
         $message = removeEmail($customerEmails);
         $message == "successful"? header("Location:emailNotification_lists.php?id=$notificationId&removed=removed"):$message;
      }


    $allNotifications = getAllNotificationId();
    $label = array_search($notificationId,$allNotifications);
    echo "<br><div><b>$label</b></div><br>";

    if(isset($_GET['updated'])){
       echo "<b>Email has been successfully updated.</b><br><br>";
    }

    elseif(isset($_GET['removed'])){
       echo "<b>Email has been successfully removed.</b><br><br>";
    }

    elseif(isset($_GET['added'])){
       echo "<b>Email has been successfully added.</b><br><br>";
    }
    
    echo displayEmails($customerEmails,$notificationId);   

?>
</body>
</head>
</html>
