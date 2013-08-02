<?php

    include 'dbcon.php';
    include 'notification_functions.php';
    include 'notificationEvent_functions.php';

    $allEmails = getAllEmails();
    $splitEmails = splitEmails($allEmails);

    //var_dump($splitEmails);
    //$uniqueEmails = allUniqueEmails();
    //var_dump($uniqueEmails);
    //$notifications = listNotifications('jbandaya@globetel.com.ph');
    //var_dump($notifications);

    /**$display = getSearchEmail('restituto.tumalad@gmail.com');
    echo $display;**/

    //$replace = displayReplaceEmailForm('restituto.tumalad@gmail.com');
    //echo $replace;

    //$result = splitEmailbyNotificationId('fortest@gmail.com');
    //var_dump($result);

    //$result = replaceEmail('fortest@gmail.com','mimieTest@gmail.com');
    //var_dump($result);

    //$result = removeAllNotifications('fortest@gmail.com');
    //var_dump($result);

    //$result = updateTicketCreate(3826,'test3@gmail.com');
    //var_dump($result);

    //$result = updateCustomerTickets(3826,'test3@gmail.com');

    //$result = updateTicketNotifications(3826,'fortest@gmail.com','mimie_test@gmail.com');
    //$result = removeTicketNotifications(3826,'fortest@gmail.com');

    /**$emptyList = getEmptyEmailList();

    echo "<pre>";
    print_r($emptyList);
    echo "</pre>";**/
    
    //$customerIds = getAllCustomerId();
    //var_dump($customerIds);

    //$displayCustomerId = displayCustomerIds();
    //echo $displayCustomerId;

    /*$subject = subject();

   
    echo "<pre>";
    print_r($subject);
    echo "</pre>";*/

    /*$companyMessage = getCompanyMessage();
    var_dump($companyMessage);*/

/**   $subject = subject();

   $compSubject = $subject["company"]; 
   $custSubject = $subject["customer"];
   $createSubject = $subject["create"];
   $closedSubject = $subject["closed"];


  addCompanyEvent('testCompany',$compSubject);
  addCustomerEvent('testCompany',$custSubject);
  addTicketCreateEvent('testCompany',$createSubject);
  addTicketClosedEvent('testCompany',$closedSubject);
**/
  /**$ids = array();
  $ids = array('3834','4621');
  $result = removeEmailperCustomer($ids,'mytest@gmail.com');
  var_dump($result);**/

  /**$notificationIds = array('3826','3830','3834');
  addMultipleNotifications($notificationIds,'mytest@gmail.com');**/

  $matches = matchEmail("fmcc");
  var_dump($matches);
  
?>
