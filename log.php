<?php


$res = "akuntansi audit bank akuntan auditor akuntan audit";


echo $res . "<br>";

$string = implode(' ', array_unique(explode(' ', $res)));


echo $string;

?>
