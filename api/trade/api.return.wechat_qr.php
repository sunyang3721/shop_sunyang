<?php
$_filename = basename(__FILE__, '.php');
list(, $method, $driver) = explode(".", $_filename);
define('_PAYMENT_', $driver);
$_GET['m'] = 'pay';
$_GET['c'] = 'index';
$_GET['a'] = 'd'.$method;
include dirname(__FILE__).'/../../index.php';