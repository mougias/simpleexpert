<?php 

DEFINE('DS', DIRECTORY_SEPARATOR);

error_reporting(E_ALL);

function __autoload($class_name) {
	require_once('classes'.DS.'Class.'.$class_name.'.php');
}



if (empty($_POST['user_input']))
    require('tmpl'.DS.'start.php');
else {
    $debugger = new Debugger($_POST['user_input']);
    $debugger->run();
} 
