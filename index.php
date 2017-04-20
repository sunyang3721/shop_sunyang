<?php
define('CHARSET', 'utf-8');
define('DOC_ROOT', str_replace("\\", '/', dirname(__FILE__) ).'/');
define('APP_PATH', DOC_ROOT.'system/');
define('APP_DEBUG', false);
include APP_PATH.'base.php';