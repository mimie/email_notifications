<html>
<head>
<title>List of Customers</title>
<link rel="stylesheet" type="text/css" href="design.css">
</head>
<body>
<?php

    include 'dbcon.php';
    include 'notification_functions.php';

    $list_customer = getCustomerList();

    $internal_customers = array();
    $external_customers = array();

    foreach($list_customer as $customer){
       $type = checkCustomerType($customer);
      
       if($type == 1){
          $external_customers[] = $customer;
       }

       else{
          $internal_customers[] = $customer;
       }
     
     }

   $searchForm = displaySearchForm();
   echo $searchForm;

   $emailSearchForm = emailSearchForm();
   echo $emailSearchForm;

   $customer_name = $_POST["customer_name"];
   $customer_type = $_POST["customer_type"];
    
   if($_POST['searchCustomer'] != 'SEARCH'){
      $allCustomers = displayCustomers($list_customer);
      
      echo "<b>ALL CUSTOMERS</b><br><br>";

      if($_POST['addEmail'] == 'ADD EMAIL' && $_POST["notifications"] && $_POST["newEmail"]){

         $notificationIds = $_POST["notifications"];
         $email = $_POST["newEmail"];

         $result = addMultipleNotifications($notificationIds,$email);
   
         echo $result;   
       }

      elseif($_POST['addEmail'] == 'ADD EMAIL' && !$_POST["notifications"]){
        echo "<b>Please select customer name to be added in the notification.<br><br></b>";
      }
       
      echo $allCustomers;
    }
  elseif($_POST['searchCustomer'] == 'SEARCH' && ($customer_name==''||$customer_name) && $customer_type=='empty'){
      $result = getEmptyEmailList();
      $emptylist = array();
      foreach($result as $customerName=>$id){
         $emptylist[] = $customerName;
      }    
      echo "<b>CUSTOMER WITH EMPTY EMAIL LIST</b><br><br>";
      $customers = displayCustomers($emptylist);
      echo $customers;

   }
   
    
   elseif($customer_name=='' && $customer_type=='external'){
       $customers = displayCustomers($external_customers);
       echo "<b>EXTERNAL</b><br><br>";
       echo $customers;
    }

    elseif($customer_name=='' && $customer_type=='internal'){
       $customers = displayCustomers($internal_customers);
       echo "<b>INTERNAL</b><br><br>";
       echo $customers;
    }

    elseif($customer_name!=''){
       $result = getSearchResult($customer_name,$customer_type);
       $label = strtoupper($customer_type);
       if($label == 'SELECT'){
          $label = "EXTERNAL AND INTERNAL";
       }
       echo "<b>".$label."</b><br><br>";
       $customers = displayCustomers($result);
       echo $customers;
    }

   elseif($customer_name=='' && $customer_type!='empty' && $_POST['searchCustomer'] == 'SEARCH'){
       $customers = displayCustomers($list_customer);
       echo "<b>ALL CUSTOMERS</b><br><br>";
       echo $customers;
   }
  
   elseif($customer_name=='' && $customer_type!='select'){
       $result = getSearchResult($customer_name,$customer_type); 
       $label = strtoupper($customer_type);
       echo "<b>".$label."</b><br><br>";
       $customers = displayCustomers($result);
       echo $customers;
   }



?>
</body>
</html>

