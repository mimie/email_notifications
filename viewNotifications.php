<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="design.css" />
</head>
<body>
<?php

  include 'dbcon.php';
  include 'notification_functions.php';

  $customerSearchForm = displaySearchForm();
  echo $customerSearchForm;

  $emailSearchForm = emailSearchForm();
  echo $emailSearchForm;

  $email = $_GET['emailVal'];

  echo "<div><h2>EMAIL NOTIFICATIONS OF $email</h2></div><br>";

  $notifications = listNotifications($email);
  $displayNotifications = displayNotifications($notifications);
  $ids = array();


  if($_POST['removeNotification']){

     $ids = $_POST["notifications"];
     $size = count($ids);
     
     if($size==0){
       echo "<div><b>Please select notifications to be removed.</b></div><br>";
     }

     else{
       $message = removeEmailperCustomer($ids,$email);
       $message == 'successful'? header("Location:viewNotifications.php?emailVal=$email") : $message;
       echo $message;
     }
     
  }

  echo $displayNotifications;
?>
</body>
</html>
