<?php
include_once 'CurlRequest.php';

$a = new CurlRequest();
$bbb = $a->Request('POST','http://www.weiyingjiashop.com/',[2],['header'=>'Content-Type: application/json']);	
var_dump($bbb);
