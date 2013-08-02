<html>
<head>
<title>Notification Events</title>
<link rel="stylesheet" type="text/css" href="design.css">
</head>
<body>
<?php
  
    include 'dbcon.php';
    include 'notification_functions.php';
    include 'notificationEvent_functions.php';

    $notificationEventForm = notificationEventForm();
    echo $notificationEventForm;

    if($_POST["addEvent"]){
       $customerId = $_POST["customerId"];
       addNotificationEvent($customerId);    
    }

    $displayCustomerId = displayCustomerIds();
    echo $displayCustomerId;
?>
</body>
</html>
