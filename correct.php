<?php
  include 'dbcon.php';

  $sql = "SELECT notification_id FROM notification_event_item WHERE event_key='CustomerID'\n"
       . "AND event_value LIKE '%@%'";

  $result = mysql_query($sql) or die(mysql_error());

  $notificationIds[] = array();

  while($row = mysql_fetch_assoc($result)){
        $notificationIds[] = $row["notification_id"];

  }

  $combine = array();

  foreach($notificationIds as $id){

    $query = "SELECT name FROM  notification_event WHERE id = '$id'";
    $result = mysql_query($query) or die(mysql_error());

    while($row = mysql_fetch_assoc($result)){
            $name = $row["name"];
            $explode = explode('_',$name);
            $combine[$id] = $explode[0];

            unset($explode);
    }

  }

  foreach($combine as $id => $eventValue){
    $update = "UPDATE notification_event_item SET event_value = '$eventValue' WHERE notification_id = '$id' AND event_key='CustomerID'";
    mysql_query($update) or die(mysql_error());
  }

?>
