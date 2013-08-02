<html>
<head><title></title>
<link rel="stylesheet" type="text/css" href="design.css">
</head>
<body>
<?php
/*functions for list_customers main class
 *@author Karen Mae Convicto
 */

/**
 *it will return customer names with format (COMPANY,COMPANY_CUSTOMER)
 *@return all customer names
 */
function getCustomerList(){

  $sql_customer = "SELECT name FROM notification_event";
  $result_customers = mysql_query($sql_customer) or die(mysql_error());

  $customers = array();

  while($customer = mysql_fetch_assoc($result_customers)){

      $customer_name = $customer["name"];
      
      $regexp2 = "/([[:alnum:]])*_CLOSED$/";
      $regexp3 = "/([[:alnum:]])*_CREATE$/";
  
      $match2 = preg_match($regexp2,$customer_name);
      $match3 = preg_match($regexp3,$customer_name);

      if($match2!=1 && $match3!=1){
       
         $customers[] = $customer["name"];
      }
  }

  return $customers;

}

/*
 *@return 1-external customer 0-internal customer
 */
function checkCustomerType($customer_name){

  $regexp1 = "/([[:alnum:]])*_CUSTOMER$/";
  $regexp2 = "/([[:alnum:]])*CUSTOMER_CLOSED$/";
  $regexp3 = "/([[:alnum:]])*CUSTOMER_CREATE$/";

  $match1 = preg_match($regexp1,$customer_name);
  $match2 = preg_match($regexp2,$customer_name);
  $match3 = preg_match($regexp3,$customer_name);

  if($match1==1 && $match2==0 && $match3==0){
     $value = 1;
  }

  else{
     $value = 0;
  }
  
  return $value;
}

/*
 *@return notification Id of a certain customer name
 */
function getNotificationId($customer_name){

  $sql_notificationId = "SELECT id FROM notification_event WHERE name='$customer_name'";
  $result_notificationId = mysql_query($sql_notificationId) or die(mysql_error());

  $row_id = mysql_fetch_row($result_notificationId);
  $notificationId = $row_id[0];

  return $notificationId;
}

/*
 *@return an array with key as a customer name and value as a notification Id
 */
function getAllNotificationId(){

  $sql_customer = "SELECT name,id FROM notification_event";
  $result_customer = mysql_query($sql_customer) or die(mysql_error());

  $companies = array();
  $ids = array();

  while($row = mysql_fetch_assoc($result_customer)){
       $companies[] = $row["name"];
       $ids[] = $row["id"];  

  }

  $customer_names = array_combine($companies,$ids);

  return $customer_names;

}

/*
 *list_customers is the array of customer names
 *@return html table format of all customers
 */
function displayCustomers(array $list_customers){

  $cust_table = "<form action='' method='post'>"
              . "<table id='customer'>"
              . "<th>Customer Name</th>"
              . "<th>Display Emails</th>"
              . "<th>Add Notification to Email</th>"
              . "<tr><td align='right' colspan='3'>"
              . "<label>Email </label>"
              . "<input name='newEmail' type='text' placeholder='user@example.com' required>"
              . "<input type='submit' name='addEmail' value='ADD EMAIL'></td></tr>";

  $customerIds = getAllNotificationId();

  foreach($list_customers as $customer_name){


    //$notificationId = getNotificationId($customer_name);
    $notificationId = $customerIds[$customer_name];

    $cust_table = $cust_table."<tr><td>".$customer_name."</td>"
                . "<td>"
                . "<a href='emailNotification_lists.php?id=".$notificationId."'>Email Lists</a>"
                . "</td>"
                . "<td><input type='checkbox' name='notifications[]' value='$notificationId'></td>"
                . "</tr>"    
                . "</form>";

  }

  $cust_table = $cust_table."</table>";

  return $cust_table;
}

/*
 *@return a customer search html form
 */
function displaySearchForm(){

   $searchForm_html = "<form action='list_customers.php' method='post'>"
                    . "Customer name: <input type='text' name='customer_name'>"
                    . "<select name='customer_type'>"
                    . "<option selected='selected' value='select'>Select customer type</option>"
                    . "<option disabled>--</option>"
                    . "<option value='external'>EXTERNAL</option>"
                    . "<option value='internal'>INTERNAL</option>"
                    . "<option value='empty'>NO EMAIL LIST</option>"
                    . "</select>"
                    . "<input type='submit' name='searchCustomer' value='SEARCH'>"
                    . "<a href='notification_event.php'><input type='button' name='notificationEvent' value='GO TO NOTIFICATION EVENT'></a>"
                    . "</form>";

    return $searchForm_html;

}

/*
 *customer_name is the pattern of string entered into customer search form
 *customer_type is the customer type selected in the option value of the customer search form
 */
function getSearchResult($customer_name,$customer_type){

    $sql_customers = "SELECT id,name FROM notification_event WHERE name LIKE '%$customer_name%'";
    $result_customers = mysql_query($sql_customers) or die(mysql_error());

    $customers = array();
    $internal = array();
    $external = array();

    $regexp2 = "/([[:alnum:]])*CUSTOMER_CLOSED$/";
    $regexp3 = "/([[:alnum:]])*CUSTOMER_CREATE$/";

    while($customer = mysql_fetch_assoc($result_customers)){

       $customer = $customer["name"];
       $match2 = preg_match($regexp2,$customer);
       $match3 = preg_match($regexp3,$customer);
     
       if($match2 == 0 && $match3 ==0){
       
         $customers[] = $customer; 
       }
    }

    foreach($customers as $customerName){
       $type = checkCustomerType($customerName);

       if($type == 1){
          $external[] = $customerName;
       }

       else{
          $internal[] = $customerName;
       }
    }

    if($customer_type == 'external'){
         return $external;
    }

    elseif($customer_type == 'select'){
        return $customers;
    }

    else{
        return $internal;
    }


}

/*
 *@return array of emails in the form notification Id as the key
 *        and value as the emails separated in comma
 */
function getAllEmails(){

   $sql_emails = "SELECT notification_id,event_value FROM notification_event,notification_event_item"
               . " WHERE id=notification_id AND event_key = 'RecipientEmail'";
   $result_emails = mysql_query($sql_emails) or die(mysql_error());
 
   $notificationIds = array();
   $emails = array();

   while($emailList = mysql_fetch_assoc($result_emails)){
      
      $notificationIds[] = $emailList["notification_id"];
      $emails[] = $emailList["event_value"];
      
  }

  $array_emails = array_combine($notificationIds,$emails);

  return $array_emails;

}

/*
 *array of all emails is the array in which emails are already splitted
 *notification id is the id of the customer
 *@return an array of emails per customer
 */
function getEmailsPerCustomer(array $array_emails,$notificationId){

  $result_emails = array();

  return $array_emails[$notificationId];

}

/*
 *array_emails is the array from getAllEmails()
 *@return all emails in which the notification Id is the key
 *        and values are the split emails
 */
function splitEmails(array $array_emails){

  $result_emails = array();
  $temp_emails = array();

  foreach($array_emails as $key => $values){

     $list_emails = explode(',',$values);

     foreach($list_emails as $email){
        $temp_emails[] = $email;

        $result_emails[$key] = $temp_emails;
     }

     unset($list_emails);
     unset($temp_emails);

  }

  return $result_emails;

}

/*
 *emails are the array of emails from getEmailsPerCustomer()
 *notification Id is the id of customer
 *@return html table of emails per customer
 */
function displayEmails(array $emails,$notificationId){

  $length = count($emails);
     
  if($length!=0){
    
    $email_table = "<table id='customer'>"
                 . "<th>Emails</th>"
                 . "<th>Notifications</th>"
                 . "<th>Edit</th>"
                 . "<th>Remove</th>";
  
    foreach($emails as $email){

       $email_table = $email_table."<tr>"
                    . "<td>".$email."</td>"
                    . "<td><a href='viewNotifications.php?emailVal=$email'>View All Notifications</a></td>"
                    . "<td><a href='emailNotification_lists.php?id=".$notificationId."&email=".$email."'>Edit</a></td>"
                    . "<td><a href='emailNotification_lists.php?id=".$notificationId."&emailRemoved=".$email."'>Remove</a></td>"
                    . "</tr>";
       }

    $email_table = $email_table."</table>"; 
  }

  elseif($length==0){
    $email_table = "<table><tr>"
                 . "<td>No email recipients available.</td>"
                 .  "</tr></table>";
       }

  return $email_table;
}

/*
 *@return html form for adding new email per customer
 */
function displayEmailForm($notificationId){
  
    $email_form = "<div id='addForm'>"
                . "<form action='' method='post'>"
                . "<label for='email'>New Email</label>"
                . "<input type='text' name='email' placeholder='email address' required>"
                . "<input type='submit' name='addEmail' value='ADD'>"
                . "</form>"
                . "</div>";

    return $email_form;
}

/*
 *notificationId is the id of the customer
 *email is the new email to be added
 *customerEmails are array of emails of a specific customer
 */
function addEmail($notificationId,$email,$customerEmails){

    $emailList = getAllEmails();
    $allEmails = splitEmails($emailList);

    if(!isValidEmail($email)){
      return $email." is an invalid email format.";
    }

    elseif(in_array($email,$customerEmails)){
       return $email." is already in the list of email recipients.";
    }

    else{
      addTicketNotifications($notificationId,$email);
      $emailsByComma = $emailList[$notificationId].",".$email;

      $sql_update = "UPDATE notification_event_item\n"
                  . "SET event_value='$emailsByComma'\n"
                  . "WHERE notification_id='$notificationId'\n"
                  . "AND event_key='RecipientEmail'";

      mysql_query($sql_update) or die(mysql_error());
      
      //return $email." is successfully added.";
      return "successful";
   }

}

/*
 *check if the email address is in valid format
 *@return boolean(true or fals) 
 */
function isValidEmail($email){ 
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function displayEditForm($email_update){

    $notificationId = $_GET['id'];

    $edit_form = "<div id='editForm'>"
               . "<form action='' method='post'>"
               . "<label for='email'>Update Email</label>"
               . "<input type='text' name='newEmail' value='$email_update' placeholder='email address' required>"
               . "<input type='submit' name='updateEmail' value='UPDATE'>"
               . "</form>"
               . "</div>";

    return $edit_form;    
}

/*
 *replace the email that is already existing in a specific customer
 *customerEmails is the array of emails of a specific customer
 */
function updateEmail($customerEmails,$newEmail){

   $count = 0;
   $count =(int)$count;

   foreach($customerEmails as $email){
     if($email == $newEmail){
        $count = 1;
        break;
     }
   }

   if(!isValidEmail($newEmail)){
     return "$newEmail is not a valid email.";
   }

   elseif($count == 1){
     return "$newEmail is already in the list of email recipients.";
   }
  
   else{
     $oldEmail = $_GET['email'];
     $notificationId = $_GET['id'];
     $toDelete = $oldEmail;

     $emails = array_diff($customerEmails,array($toDelete));
     $emails[] = $newEmail;

     $updatedEmail = implode(',',$emails);

     $sql_update = "UPDATE notification_event_item\n"
                 . "SET event_value = '$updatedEmail'\n"
                 . "WHERE notification_id = '$notificationId'\n"
                 . "AND event_key ='RecipientEmail'";
           
     mysql_query($sql_update) or die(mysql_error());

     updateTicketNotifications($notificationId,$oldEmail,$newEmail);

     return "successful";

  }

}

/*
 *remove an email that is already existing in a specific customer
 *customerEmails is the array of emails of a specific customer
 */
function removeEmail($customerEmails){

   $deleteEmail = $_GET['emailRemoved'];
   $notificationId = $_GET['id'];
   $emails = array_diff($customerEmails,array($deleteEmail));
   
   $updatedEmail = implode(',',$emails);

   $sql_update = "UPDATE notification_event_item\n"
               . "SET event_value = '$updatedEmail'\n"
               . "WHERE notification_id = '$notificationId'\n"
               . "AND event_key ='RecipientEmail'";
           
   mysql_query($sql_update) or die(mysql_error());

   removeTicketNotifications($notificationId,$deleteEmail);

   return "successful";
}

/*
 *@return an array of unique emails that is listed in the email notifications
 */
function allUniqueEmails(){

   $allEmails = getAllEmails();
   $splitEmails = splitEmails($allEmails);

   $allUniqueEmails = array();

   foreach($splitEmails as $notificationId=>$emails){

      foreach($emails as $values){
         $allUniqueEmails[] = $values;
      }
   }

   $allUniqueEmails = array_unique($allUniqueEmails);

   return $allUniqueEmails;
}

/*
 *email is the email that is listed in a specific customer
 *@return listNotification in which the key is the notification id
 *        value is the customer name
 */
function listNotifications($email){
    
   $listNotification = array();

   $sql_notification = "SELECT name,notification_id FROM notification_event,notification_event_item\n"
                     . "WHERE event_key='RecipientEmail'\n"
                     . "AND event_value LIKE '%$email%'\n"
                     . "AND notification_id=id";
   $notifications = mysql_query($sql_notification) or die(mysql_error());

   while($row = mysql_fetch_assoc($notifications)){
        
       $id = $row['notification_id']; 
       $name = $row['name'];

       $listNotification[$id] = $name; 
    }

   return $listNotification;
}

/*
 *@return an html table of all unique emails in the list
 */
function displayAllEmails(){

   $allEmailsDisplay = "<table id='customer'>"
                     . "<th>Emails</th>"
                     . "<th>Notifications</th>"
                     . "<th>Remove Notifications</th>"
                     . "<th>Replace Email</th>";
   $uniqueEmails = allUniqueEmails();

   foreach($uniqueEmails as $email){
     $allEmailsDisplay = $allEmailsDisplay.'<tr>'
                       . "<td>$email</td>"
                       . "<td><a href='viewNotifications.php?emailVal=$email'>View Notifications</a></td>"
                       . "<td><a href='emails.php?emailRemoved=$email'>Remove</a></td>"
                       . "<td><a href='emails.php?emailVal=".$email."'>Replace Email</a></td>"
                       . "</tr>";
      
   }

   $allEmailsDisplay = $allEmailsDisplay.'</table>';

   return $allEmailsDisplay;

}

/*
 *@return an html form for searching an email
 */
function emailSearchForm(){

   $emailSearch = "<div id='emailSearch'>"
                . "<form action='emails.php' method='post'"
                . "<label for='email'>Email</label>"
                . "<input type='text' name='email' placeholder='email address'>"
                . "<input type='submit' name='searchEmail' value='SEARCH'>"
                . "<a href='emails.php'><button>VIEW EMAILS</button></a>"
                . "</form>"
                . "</div>";

   return $emailSearch;
}

/*
 *email is the email that is entered in the email search form
 *@return the html table of the email if the email searched exists
 *@return a message if the email is not listed in the notifications
 */
function getSearchEmail($email){

   $validate = isValidEmail($email);

   Switch($validate){
   
     case true;
   
       $allEmails = allUniqueEmails();
       if(in_array($email,$allEmails)){
         $emailResult = array("$email");
         $emailsDisplay = emailSearchDisplay($emailResult);
         return $emailsDisplay;
        }
       else{

         return "Email recipient is not listed.";
        }
     break;

     case false;

       $matches = matchEmail($email);

       if($matches){
         $emailsDisplay = emailSearchDisplay($matches);
         return $emailsDisplay;
        }

       else{
         return "Email recipient is not listed.";
       }
   
   }

}

/*
 *email is the pattern that is searched
 *@return array of email matches
 */
function matchEmail($email){

  $allEmails = allUniqueEmails();
  $emailMatches = array();

  foreach($allEmails as $uniqueEmail){

    $string = $uniqueEmail;
    $result = preg_match("/$email/", $string);

    if($result == 1){
       $emailMatches[] = $uniqueEmail;
    }

  }

  return $emailMatches;
}
/*
 *emailResult is array of email matches
 *@return table html of email matches
 */
function emailSearchDisplay(array $emailResult){
   $emailsDisplay = "<table id='customer'>"
                     . "<th>Emails</th>"
                     . "<th>Notifications</th>"
                     . "<th>Remove Notifications</th>"
                     . "<th>Replace Email</th>";

   foreach($emailResult as $email){
     $emailsDisplay = $emailsDisplay.'<tr>'
                       . "<td>$email</td>"
                       . "<td><a href='viewNotifications.php?emailVal=$email'>View Notifications</a></td>"
                       . "<td><a href='emails.php?emailRemoved=$email'>Remove</a></td>"
                       . "<td><a href='emails.php?emailVal=".$email."'>Replace Email</a></td>"
                       . "</tr>";
   }

   $emailsDisplay = $emailsDisplay.'</table>';

   return $emailsDisplay;

}

/*
 *notifications is the array of notifications of a specific email
 *notifications parameter must come from listNotifications() function
 *@return a html table of the notification a specific email
 */
function displayNotifications(array $notifications){

   $email = $_GET['emailVal'];

   $notificationDisplay = "<table id='customer'>"
                        . "<th>Companies</th>"
                        . "<th>Display Emails</th>"
                        . "<th>Remove Notifications</th>";
   $notificationDisplay = $notificationDisplay."<form action='' method='post'>"
                        . "<tr align='right'>"
                        . "<td colspan='3'><input name='reset' type='reset' value='RESET'><input type='submit' name='removeNotification' value='REMOVE'></td>"
                        . "</tr>";

   foreach($notifications as $notificationId=>$company){
       
       $notificationDisplay = $notificationDisplay."<tr>"
                            . "<td>$company</td>"
                            . "<td><a href='emailNotification_lists.php?id=$notificationId'>Email Lists</td>"
                            . "<td><input type='checkbox' name='notifications[]' value='$notificationId'></td>"
                            . "</tr>";
   }
                    
  $notificationDisplay = $notificationDisplay."</table>"
                       . "</form>";
  
  return $notificationDisplay;
}

/*
 *@return a html form for replacing an email
 */
function displayReplaceEmailForm($email){

  $email = htmlspecialchars($email);

  $replaceEmails = "<form action='' method='post'>"
                 . "<label><b>Replace <i>$email </i>to:</b></label>"
                 . "<input type='text' name='newEmail' placeholder='$email' required>"
                 . "<input type='submit' name='replace' value='REPLACE'>"
                 . "</form>";

  return $replaceEmails;

}

/**
 *oldEmail is the email that must be replaced
 *@return array of emails per notification Id
 *key is the notificationId and values are the array of split emails
 *[notificationId] => $emails
 */
function splitEmailbyNotificationId($oldEmail){

  $notifications = array();
  $tempEmails = array();
  $ids = array();
  $emails = array();
  $oldEmail = mysql_real_escape_string($oldEmail);
      
  $sqlValue = "SELECT notification_id,event_value FROM notification_event_item\n"
            . "WHERE event_key='RecipientEmail' AND event_value LIKE '%{$oldEmail}%'";
  $result = mysql_query($sqlValue) or die(mysql_error());

  while($row = mysql_fetch_assoc($result)){
     $ids[] = $row["notification_id"];
     $emails[] = $row["event_value"];       
  }

  $combine = array_combine($ids,$emails);

  foreach($combine as $id=>$eventValue){

    $tempEmails = explode(',',$eventValue);
    $notifications[$id] = $tempEmails;
    
    unset($tempEmails);
  }

  return $notifications;
}

/*
 *replace an email in all notifications
 *oldEmail is the email to be replaced
 *newEmail is the email that must be a replacement of the old email
 */
function replaceEmail($oldEmail,$newEmail){

  $notifications = splitEmailbyNotificationId($oldEmail);
  $toDelete = $oldEmail;

  if(!isValidEmail($newEmail)){
    return "<b><i>$newEmail</i> is an invalid email address format.</b>";
  }

  elseif(isValidEmail($newEmail)){

   foreach($notifications as $id=>$emails){
    $emailList = array_diff($emails,array($toDelete));
    $emailList[] = $newEmail;

    $emails = implode(',',$emailList); 
    $emails = mysql_real_escape_string($emails);
  
    $sql_update = "UPDATE notification_event_item\n"
                . "SET event_value = '{$emails}'\n"
                . "WHERE notification_id = '$id'\n"
                . "AND event_key ='RecipientEmail'";
           
    mysql_query($sql_update) or die(mysql_error());
      
   }
   $oldEmail = htmlspecialchars($oldEmail);
   $newEmail = htmlspecialchars($newEmail);

   return "successful";
  }

}

/*
 *email is the email of the customer
 *remove the email in all notifications
 *@return successful if removing an email has no errors
 */
function removeAllNotifications($email){

  $notifications = splitEmailbyNotificationId($email);
  $toDelete = $email;

   foreach($notifications as $id=>$emails){
    $emailList = array_diff($emails,array($toDelete));

    $emails = implode(',',$emailList); 
    $emails = mysql_real_escape_string($emails);
  
    $sql_update = "UPDATE notification_event_item\n"
                . "SET event_value = '{$emails}'\n"
                . "WHERE notification_id = '$id'\n"
                . "AND event_key ='RecipientEmail'";
           
    mysql_query($sql_update) or die(mysql_error());
      
   }

   return "successful";
  }

/*
 *ids is the array of the notification id of the customer in which an email is listed
 *email is the email that must be removed
 *@return successful if no errors encountered in removing the email
 *@return an error message if removing an email is unsuccessful
 */
function removeEmailperCustomer(array $ids,$email){

  foreach($ids as $notificationId){

    $sql = "SELECT event_value FROM notification_event_item\n"
         . "WHERE notification_id = $notificationId\n"
         . "AND event_key = 'RecipientEmail'";

    $result = mysql_query($sql) or die(mysql_error());

    $row = mysql_fetch_assoc($result);
    $emails = $row["event_value"];

    $splitEmails = explode(',',$emails);

    $splitEmails = array_diff($splitEmails,array($email));
    $updatedEmails = implode(',',$splitEmails);
    $updatedEmails = mysql_real_escape_string($updatedEmails);

    $sql_update = "UPDATE notification_event_item\n"
                . "SET event_value='{$updatedEmails}'\n"
                . "WHERE notification_id = $notificationId\n"
                . "AND event_key = 'RecipientEmail'";
   
    $result = mysql_query($sql_update) or die(mysql_error());

   }

    if($result){
      return "successful";
    }

    else{
      return "An error occurred in removing the notifications.";
    }

}

/*
 *this function is for adding multiple notifications of specific email
 *this will add an email to one or more notifications of one or more customers
 *@return an updated email notification list
 */
function addTicketNotifications($notificationId,$email){

    $notifications = getAllNotificationId();
    $emailList = getAllEmails();
    $customerName = array_search($notificationId,$notifications);

    $match = checkCustomerType($customerName);

    if($match==1){

    //customer name for ticket close
    $customerClosed = $customerName."_CLOSED";
    //customer name for ticket create
    $customerCreate = $customerName."_CREATE";

    //notification Id of CUSTOMER_CLOSED
    $idClosed = $notifications[$customerClosed];
    //notification Id of CUSTOMER_CREATE
    $idCreate = $notifications[$customerCreate];

    $emailClosed = $emailList[$idClosed].",".$email;
    $emailCreate = $emailList[$idCreate].",".$email;

    $emailClosed = mysql_real_escape_string($emailClosed);
    $emailCreate = mysql_real_escape_string($emailCreate);

    $sql_close = "UPDATE notification_event_item\n"
               . "SET event_value='{$emailClosed}'\n"
               . "WHERE notification_id='$idClosed'\n"
               . "AND event_key='RecipientEmail'";
       
    mysql_query($sql_close) or die(mysql_error());
    
    $sql_create = "UPDATE notification_event_item\n"
                . "SET event_value='{$emailCreate}'\n"
                . "WHERE notification_id='$idCreate'\n"
                . "AND event_key='RecipientEmail'";
       
    mysql_query($sql_create) or die(mysql_error());

    }

}

/*
 *this function is used when an email list of an external customer is updated
 *it should also update CUSTOMER_CREATE and CUSTOMER_CLOSED
 *notification id of the customer
 *oldEmail is the email that is to be replaced
 *newEmail is the email as a replacement to the oldEmail
 *@return an updated list of email CUSTOMER_CREATE and CUSTOMER_CLOSED
 */
function updateTicketNotifications($notificationId,$oldEmail,$newEmail){

    $notifications = getAllNotificationId();
    $emailList = getAllEmails();
    $customerName = array_search($notificationId,$notifications);

    $match = checkCustomerType($customerName);

    if($match==1){

       //customer name for ticket close
       $customerClosed = $customerName."_CLOSED";
       //customer name for ticket create
       $customerCreate = $customerName."_CREATE";

       //notification Id of CUSTOMER_CLOSED
       $idClosed = $notifications[$customerClosed];
       //notification Id of CUSTOMER_CREATE
       $idCreate = $notifications[$customerCreate];
       
       $allEmails = splitEmails($emailList);
       $emailClosed = getEmailsPerCustomer($allEmails,$idClosed);
       $emailCreate = getEmailsPerCustomer($allEmails,$idCreate);

       $emailClosed = array_diff($emailClosed,array($oldEmail));
       $emailCreate = array_diff($emailCreate,array($oldEmail));

       $emailClosed[] = $newEmail;
       $emailCreate[] = $newEmail;

       $updatedClosed = implode(",",$emailClosed);
       $updatedCreate = implode(",",$emailCreate);

       $updatedClosed = mysql_real_escape_string($updatedClosed);
       $updatedCreate = mysql_real_escape_string($updatedCreate);
    
       $sql_close = "UPDATE notification_event_item\n"
                  . "SET event_value='{$updatedClosed}'\n"
                  . "WHERE notification_id='$idClosed'\n"
                  . "AND event_key='RecipientEmail'";
       
       mysql_query($sql_close) or die(mysql_error());
    
       $sql_create = "UPDATE notification_event_item\n"
                   . "SET event_value='{$updatedCreate}'\n"
                   . "WHERE notification_id='$idCreate'\n"
                   . "AND event_key='RecipientEmail'";
       
       mysql_query($sql_create) or die(mysql_error());
    }

}

/*
 *notification Id is the id of a specific customer name
 *email is the email that must be removed in the notifications
 *@return an updated list of the notifications
 */
function removeTicketNotifications($notificationId,$email){

    $notifications = getAllNotificationId();
    $emailList = getAllEmails();
    $customerName = array_search($notificationId,$notifications);

    $match = checkCustomerType($customerName);

    if($match==1){
            
       //customer name for ticket close
       $customerClosed = $customerName."_CLOSED";
       //customer name for ticket create
       $customerCreate = $customerName."_CREATE";

       //notification Id of CUSTOMER_CLOSED
       $idClosed = $notifications[$customerClosed];
       //notification Id of CUSTOMER_CREATE
       $idCreate = $notifications[$customerCreate];
       
       $allEmails = splitEmails($emailList);
       $emailClosed = getEmailsPerCustomer($allEmails,$idClosed);
       $emailCreate = getEmailsPerCustomer($allEmails,$idCreate);

       $emailClosed = array_diff($emailClosed,array($email));
       $emailCreate = array_diff($emailCreate,array($email));
       
       $updatedClosed = implode(",",$emailClosed);
       $updatedCreate = implode(",",$emailCreate);
    
       $sql_close = "UPDATE notification_event_item\n"
                  . "SET event_value='{$updatedClosed}'\n"
                  . "WHERE notification_id='$idClosed'\n"
                  . "AND event_key='RecipientEmail'";
       
       mysql_query($sql_close) or die(mysql_error());
    
       $sql_create = "UPDATE notification_event_item\n"
                   . "SET event_value='{$updatedCreate}'\n"
                   . "WHERE notification_id='$idCreate'\n"
                   . "AND event_key='RecipientEmail'";
       
       mysql_query($sql_create) or die(mysql_error());
    }

}

/*
 *@return an array of customer name as a key
 *        and value is the notification id
 *        of all customer with an empty email list
 */
function getEmptyEmailList(){

   $sql = "SELECT notification_id,name FROM notification_event,notification_event_item\n"
        . "WHERE notification_id = id\n"
        . "AND event_key = 'RecipientEmail'\n"
        . "AND event_value = ''";

   $result = mysql_query($sql) or die(mysql_error());
   
   $customerNames = array();
   $notificationIds = array();

   $regexp2 = "/([[:alnum:]])*CUSTOMER_CLOSED$/";
   $regexp3 = "/([[:alnum:]])*CUSTOMER_CREATE$/";

   while($row = mysql_fetch_assoc($result)){
    $customer = $row["name"];

   $match2 = preg_match($regexp2,$customer);
   $match3 = preg_match($regexp3,$customer);

   
   if($match2==0 && $match3==0){
       $customerNames[] = $customer;
       $id = $row["notification_id"];
       $notificationIds[] = $id;
    }
       
   }
   
  $customerList = array_combine($customerNames,$notificationIds);
  return $customerList;
 
}

/*
 *@return an html table of the empty email list
 */
function displayEmptyEmailList(){

  $listCustomer = getEmptyEmailList();

  $html = "<h2>Customer With Empty Email List</h2>";

  $html = $html."<table id='customer'>"
        . "<th>Customer Name</th>"
        . "<th>Add Email</th>";

  foreach($listCustomer as $name=>$id){
    $html = $html."<tr>"
          . "<td>$name</td>"
          . "<td><a href='emailNotification_lists.php?id=$id'>Add Email</a></td>"
          . "</tr>";
  }
        
  $html = $html."</table>";

  return $html;
  
}

/*
 *notificationIds is the array of notification ids 
 *that are selected to be added to a specific email
 *@return an updated email list notification
 */
function addMultipleNotifications(array $notificationIds,$email){

  $listNotifications = listNotifications($email);
  $emailList = getAllEmails();
  $count = count($notificationIds); 

  if(!isValidEmail($email)){
      return "<b>$email is an invalid email address.<br><br></b>";
   }

  elseif($count == 0){
     return "Please select customer name to be added in the notification.";
  }
 
  else{
    foreach($notificationIds as $id){
      if(!array_key_exists($id,$listNotifications)){
         addTicketNotifications($id,$email);
         $emailsByComma = $emailList[$id].",".$email;

         $sql_update = "UPDATE notification_event_item\n"
                     . "SET event_value='$emailsByComma'\n"
                     . "WHERE notification_id='$id'\n"
                     . "AND event_key='RecipientEmail'";

          mysql_query($sql_update) or die(mysql_error());

      }
    }
   
   return "<b>$email has been successfully added to email notification list.</b><br><br>";
  }
}
?>
</body>
</html>
