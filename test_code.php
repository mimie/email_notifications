<?php
    $orig=array(312, 401, 1599, 3);
    $toDelete=401;

    $array=array_diff($orig, array($toDelete));

    var_dump($array);

?>

