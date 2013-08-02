<html>
<head>
<title>Customer With No Email Recipients</title>
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

  $emptyList = displayEmptyEmailList();
  echo $emptyList; 
  
?>  
</body>
</html>
