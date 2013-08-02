<html>
<head><title></title>
<link rel="stylesheet" type="text/css" href="design.css">
</head>
<body>
<?php

 include 'dbcon.php';

 function getAllCustomerId(){
   
   $sql = "SELECT DISTINCT(event_value) FROM notification_event_item WHERE event_key='CustomerID'";
   $result = mysql_query($sql) or die(mysql_error());

   $customerIds = array();

   while($row = mysql_fetch_assoc($result)){
     $customerIds[] = $row["event_value"];
   }

   return $customerIds;
 }

 function displayCustomerIds(){

   $customerIds = getAllCustomerId();

   $html = "<table id='customer'>"
         . "<th>CUSTOMER ID</th>";

   foreach($customerIds as $customerName){

     $html = $html."<tr>"
           . "<td>$customerName</td>"
           . "</tr>";

   }

   $html = $html."</table>";

   return $html;
 }

 function notificationEventForm(){

   $html = "<div>"
         . "<form action='' method='post'>"
         . "<label for='customerId'>Customer ID</label>"
         . "<input type='text' name='customerId' placeholder='Customer ID' required>"
         . "<input type='submit' name='addEvent' value='ADD NOTIFICATION EVENT'>"
         . "<a href='list_customers.php'><input type='button' name='customer' value='GO TO CUSTOMER LIST'></a>"
         . "<form>"
         . "</div>";

   return $html;
 }

 /*
  *customerId is customer code of each company in customer_user table
  *@return successful insert of adding company to notification_event
  */
 /**function addCompany($customerId){

   

 }**/

 
 function subject(){

   $subject = array();
   
   $company = "<OTRS_CUSTOMER_DATA_UserCompany> (<OTRS_TICKET_DynamicField_CircuitID>)  Ticket No[<OTRS_TICKET_TicketNumber>]";
   $subject["company"] = replacehtml(htmlspecialchars($company));

   $customer = "<OTRS_TICKET_Title> -  <OTRS_CUSTOMER_DATA_UserCompany> (<OTRS_TICKET_DynamicField_CircuitID>)";
   $subject["customer"] = replacehtml(htmlspecialchars($customer));

   $create = "Ticket Opened -  <OTRS_CUSTOMER_DATA_UserCompany> (<OTRS_TICKET_DynamicField_CircuitID>)";
   $subject["create"] = replacehtml(htmlspecialchars($create));

   $closed = "Ticket Closed -  <OTRS_CUSTOMER_DATA_UserCompany> (<OTRS_TICKET_DynamicField_CircuitID>)";
   $subject["closed"] = replacehtml(htmlspecialchars($closed)); 

   return $subject;
 }

function getCompanyMessage(){

    $message = <<<EOT
Company Name: <OTRS_CUSTOMER_DATA_UserCompany>
Created Date/Time: <OTRS_TICKET_Created>
Current Ticket Owner: <OTRS_TICKET_OwnerName>
Ticket Createdy By: <OTRS_TICKET_CreateByName>
Ticket Age: <OTRS_TICKET_AgeFormatted>
Trouble: <OTRS_TICKET_Title>
Ticket Number: <OTRS_TICKET_TicketNumber>
CircuitID: <OTRS_TICKET_DynamicField_CircuitID>
Service: <OTRS_TICKET_Service>
Update: <OTRS_AGENT_BODY>

This is a system generated notification. Please do not reply to this email.

This e-mail message (including attachments, if any) is intended for the use of the individual or the entity to whom it is addressed and may contain information that is privileged, proprietary, confidential and exempt from disclosure. If you are not the intended recipient, you are notified that any dissemination, distribution or copying of this communication is strictly prohibited. If you have received this communication in error, please notify the sender and delete this E-mail message immediately.
EOT;

   return $message;
} 

function getCustomerMessage(){

   $message = <<<EOT
Company Name: <OTRS_CUSTOMER_DATA_UserCompany>
Circuit Name: <OTRS_TICKET_CircuitName>
Created Date/Time: <OTRS_TICKET_Created>
Current Ticket Owner: <OTRS_TICKET_OwnerName>
Ticket Createdy By: <OTRS_TICKET_CreateByName>
Ticket Age: <OTRS_TICKET_AgeFormatted>
Trouble: <OTRS_TICKET_Title>
Ticket Number: <OTRS_TICKET_TicketNumber>
CircuitID: <OTRS_TICKET_DynamicField_CircuitID>
Service: <OTRS_TICKET_Service>
Update: <OTRS_AGENT_BODY>

This is a system generated notification. Please do not reply to this email.
EOT;

  return $message;
}

function getTicketCreateMessage(){

  $message = <<<EOT
Company Name: <OTRS_CUSTOMER_DATA_UserCompany>
Circuit Name: <OTRS_TICKET_CircuitName>
Created Date/Time: <OTRS_TICKET_Created>
Current Ticket Owner: <OTRS_TICKET_OwnerName>
Ticket Createdy By: <OTRS_TICKET_CreateByName>
Ticket Age: <OTRS_TICKET_AgeFormatted>
Trouble: <OTRS_TICKET_Title>
Ticket Number: <OTRS_TICKET_TicketNumber>
CircuitID: <OTRS_TICKET_DynamicField_CircuitID>
Service: <OTRS_TICKET_Service>


This is a system generated notification. Please do not reply to this email. 
EOT;

  return $message;
}

function getTicketCloseMessage(){

  $message = <<<EOT
Company Name: <OTRS_CUSTOMER_DATA_UserCompany>
Circuit Name: <OTRS_TICKET_CircuitName>
Created Date/Time: <OTRS_TICKET_Created>
Current Ticket Owner: <OTRS_TICKET_OwnerName>
Ticket Createdy By: <OTRS_TICKET_CreateByName>
Ticket Age: <OTRS_TICKET_AgeFormatted>
Trouble: <OTRS_TICKET_Title>
Ticket Number: <OTRS_TICKET_TicketNumber>
CircuitID: <OTRS_TICKET_DynamicField_CircuitID>
Service: <OTRS_TICKET_Service>


This is a system generated notification. Please do not reply to this email. 
EOT;

  return $message;
}

function addCompanyEvent($customerId,$subject){

  $customerId = strtoupper($customerId);
  $create_time = date_create();
  $create_time = date_format($create_time, 'Y-m-d H:i:s');

  $text = getCompanyMessage();
  
  $sql_company = "INSERT INTO notification_event (name,subject,text,content_type,charset,valid_id,create_time,create_by,change_time,change_by)\n"
               . "VALUES('$customerId','$subject','$text','text/plain','utf-8','1','$create_time','2','0000-00-00 00:00:00','2')";

  mysql_query($sql_company) or die(mysql_error());

}


function addCustomerEvent($customerId,$subject){

  $customerId = $customerId."_CUSTOMER";
  $customerId = strtoupper($customerId);

  $create_time = date_create();
  $create_time = date_format($create_time, 'Y-m-d H:i:s');

  $text = getCustomerMessage();
  
  $sql_customer = "INSERT INTO notification_event (name,subject,text,content_type,charset,valid_id,create_time,create_by,change_time,change_by)\n"
                . "VALUES ('$customerId','$subject','$text','text/plain','utf-8','1','$create_time','2','0000-00-00 00:00:00','2')";

  mysql_query($sql_customer) or die(mysql_error());

}

function addTicketCreateEvent($customerId,$subject){

  $customerId = $customerId."_CUSTOMER_CREATE";
  $customerId = strtoupper($customerId);

  $create_time = date_create();
  $create_time = date_format($create_time, 'Y-m-d H:i:s');

  $text = getTicketCreateMessage();
  
  $sql_create = "INSERT INTO notification_event (name,subject,text,content_type,charset,valid_id,create_time,create_by,change_time,change_by)\n"
              . "VALUES ('$customerId','$subject','$text','text/plain','utf-8','1','$create_time','2','0000-00-00 00:00:00','2')";

  mysql_query($sql_create) or die(mysql_error());

}


function addTicketClosedEvent($customerId,$subject){

  $customerId = $customerId."_CUSTOMER_CLOSED";
  $customerId = strtoupper($customerId);

  $create_time = date_create();
  $create_time = date_format($create_time, 'Y-m-d H:i:s');

  $text = getTicketCloseMessage();
  
  $sql_close = "INSERT INTO notification_event (name,subject,text,content_type,charset,valid_id,create_time,create_by,change_time,change_by)\n"
               . "VALUES ('$customerId','$subject','$text','text/plain','utf-8','1','$create_time','2','0000-00-00 00:00:00','2')";

  mysql_query($sql_close) or die(mysql_error());

}

function replacehtml($subject){

  $subject = str_replace("&lt;","<",$subject);
  $subject = str_replace("&gt;",">",$subject);

  return $subject;
}

function getIdCompany($customerId){
   
  $customerId = strtoupper($customerId);
  $customerId = mysql_real_escape_string($customerId);
  $sql_company = "SELECT id FROM notification_event WHERE name = '$customerId'";

  $result = mysql_query($sql_company) or die(mysql_error());

  $row = mysql_fetch_assoc($result);
  $notificationId = $row["id"];

  return $notificationId;

}

function getIdCustomer($customerId){
   
  $customerId = strtoupper($customerId);
  $customerId = $customerId."_CUSTOMER";
  $customerId = mysql_real_escape_string($customerId);
  $sql_company = "SELECT id FROM notification_event WHERE name = '$customerId'";

  $result = mysql_query($sql_company) or die(mysql_error());

  $row = mysql_fetch_assoc($result);
  $notificationId = $row["id"];

  return $notificationId;

}

function getIdCreate($customerId){
   
  $customerId = strtoupper($customerId);
  $customerId = $customerId."_CUSTOMER_CREATE";
  $customerId = mysql_real_escape_string($customerId);
  $sql_company = "SELECT id FROM notification_event WHERE name = '$customerId'";

  $result = mysql_query($sql_company) or die(mysql_error());

  $row = mysql_fetch_assoc($result);
  $notificationId = $row["id"];

  return $notificationId;

}


function getIdClosed($customerId){
   
  $customerId = strtoupper($customerId);
  $customerId = $customerId."_CUSTOMER_CLOSED";
  $customerId = mysql_real_escape_string($customerId);
  $sql_company = "SELECT id FROM notification_event WHERE name = '$customerId'";

  $result = mysql_query($sql_company) or die(mysql_error());

  $row = mysql_fetch_assoc($result);
  $notificationId = $row["id"];

  return $notificationId;

}

function companyAddItem($customerId){

  $customerId = strtoupper($customerId);
  $notificationId = getIdCompany($customerId);

  $sql_attachment = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                  . "VALUES ($notificationId,'ArticleAttachmentInclude','0')";
  
  mysql_query($sql_attachment) or die(mysql_error());

  $sql_customerId = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                  . "VALUES ($notificationId,'CustomerID','$customerId')";
  mysql_query($sql_customerId) or die(mysql_error());

  $sql_events = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
              . "VALUES ($notificationId,'Events','ArticleCreate')";
  mysql_query($sql_events) or die(mysql_error());

  
  $sql_article = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
               . "VALUES ($notificationId,'ArticleTypeID','10')";
  mysql_query($sql_article) or die(mysql_error());
  
  
  $sql_notification = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                    . "VALUES ($notificationId,'NotificationArticleTypeID','4')";
  mysql_query($sql_notification) or die(mysql_error());

  
  $sql_recipient = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                 . "VALUES ($notificationId,'RecipientEmail','')";
  mysql_query($sql_recipient) or die(mysql_error());
}

function customerAddItem($customerId){

  $customerId = strtoupper($customerId);
  $notificationId = getIdCustomer($customerId);

  $sql_attachment = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                  . "VALUES ($notificationId,'ArticleAttachmentInclude','0')";
  
  mysql_query($sql_attachment) or die(mysql_error());

  $sql_customerId = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                  . "VALUES ($notificationId,'CustomerID','$customerId')";
  mysql_query($sql_customerId) or die(mysql_error());

  $sql_events = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
              . "VALUES ($notificationId,'Events','ArticleCreate')";
  mysql_query($sql_events) or die(mysql_error());

  
  $sql_article = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
               . "VALUES ($notificationId,'ArticleTypeID','10')";
  mysql_query($sql_article) or die(mysql_error());
  
  
  $sql_notification = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                    . "VALUES ($notificationId,'NotificationArticleTypeID','3')";
  mysql_query($sql_notification) or die(mysql_error());

  
  $sql_recipient = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                 . "VALUES ($notificationId,'RecipientEmail','')";
  mysql_query($sql_recipient) or die(mysql_error());

  $sql_state = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
             . "VALUES ($notificationId,'StateID','4')";
  mysql_query($sql_state) or die(mysql_error());
}

function createAddItem($customerId){

  $customerId = strtoupper($customerId);
  $notificationId = getIdCreate($customerId);

  $sql_attachment = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                  . "VALUES ($notificationId,'ArticleAttachmentInclude','0')";
  
  mysql_query($sql_attachment) or die(mysql_error());

  $sql_customerId = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                  . "VALUES ($notificationId,'CustomerID','$customerId')";
  mysql_query($sql_customerId) or die(mysql_error());

  $sql_events = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
              . "VALUES ($notificationId,'Events','TicketCreate')";
  mysql_query($sql_events) or die(mysql_error());
  
  $sql_notification = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                    . "VALUES ($notificationId,'NotificationArticleTypeID','3')";
  mysql_query($sql_notification) or die(mysql_error());

  
  $sql_recipient = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                 . "VALUES ($notificationId,'RecipientEmail','')";
  mysql_query($sql_recipient) or die(mysql_error());
}

function closedAddItem($customerId){

  $customerId = strtoupper($customerId);
  $notificationId = getIdClosed($customerId);

  $sql_attachment = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                  . "VALUES ($notificationId,'ArticleAttachmentInclude','0')";
  
  mysql_query($sql_attachment) or die(mysql_error());

  $sql_customerId = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                  . "VALUES ($notificationId,'CustomerID','$customerId')";
  mysql_query($sql_customerId) or die(mysql_error());

  $sql_events = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
              . "VALUES ($notificationId,'Events','ArticleCreate')";
  mysql_query($sql_events) or die(mysql_error());
  
  $sql_notification = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                    . "VALUES ($notificationId,'NotificationArticleTypeID','3')";
  mysql_query($sql_notification) or die(mysql_error());

  
  $sql_recipient = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
                 . "VALUES ($notificationId,'RecipientEmail','')";
  mysql_query($sql_recipient) or die(mysql_error());

  $sql_state = "INSERT INTO notification_event_item (notification_id,event_key,event_value)\n"
             . "VALUES ($notificationId,'StateID','4')";
  mysql_query($sql_state) or die(mysql_error());

}

function addNotificationEvent($customerId){

 $subjects = subject();

 $compSubject = $subjects["company"];
 $custSubject = $subjects["customer"];
 $createSubject = $subjects["create"];
 $closedSubject = $subjects["closed"];

 addCompanyEvent($customerId,$compSubject);
 addCustomerEvent($customerId,$custSubject);
 addTicketCreateEvent($customerId,$createSubject);
 addTicketClosedEvent($customerId,$createSubject);

 companyAddItem($customerId);
 customerAddItem($customerId);
 createAddItem($customerId);
 closedAddItem($customerId);
 
 echo "<b>Notification Event successfully added.</b>"; 
}
?>
</body>
</html>
