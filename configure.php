<?php

/**
 * Tool used to configure the debugger
 * 
 * @author Stefanos Demetriou
 *
 */
define ('DS', DIRECTORY_SEPARATOR);

function __autoload($class_name)
{
    require_once('classes'.DS.'Class.'.$class_name.'.php');
}


$db = DB::getInstance();

//process updates first
if (!empty($_POST['info']) && is_array($_POST['info'])) {
	foreach ($_POST['info'] as $key => $val) { // really should be one record only according to current template, but still
	   $val['id'] = $key;
	   $step = StepFactory::createFromArray($val);
	   if ($step) $step->save();
	}
}
		
// process step delete if clicked
if (!empty($_POST['deleteStep']) && is_numeric($_POST['deleteStep'])) {
    StepFactory::delete($_POST['deleteStep']);
}
		

// add new step if submitted
if (!empty($_POST['infoNew']) && is_array($_POST['infoNew'])) {
    $step = StepFactory::createFromArray($_POST['infoNew']);
    $step->save();
}		
		

// handle step move if dragged
if (!empty($_POST['moveStep'])) {
	$stepId = $_POST['stepId'];
	$newParentId = $_POST['newParentId'];

	if ($stepId && $newParentId) {
	    $step = StepFactory::fetch($stepId);
	    $step->setParentId($newParentId);
	    $step->save();
	}
}
		
		


// setup default $vars array

// step types
$vars['types'] = array(
	Debugger::STEP_TYPE_SYSTEM => 'System',
	Debugger::STEP_TYPE_JUMP => 'Jump',
	Debugger::STEP_TYPE_EVALUATE => 'Evaluate'
);
		

// load and list all systems
$vars['systems'] = array();
foreach (glob("systems/*.php") as $filename) {
	$vars['systems'][]['name'] = basename($filename,'.php');
	include $filename;
}
		
// load all methods for each system
$vars['methods'] = array();
foreach ($vars['systems'] as $key => $system) {
	$tmpMethods = get_class_methods($system['name']);
	$vars['methods'][$system['name']] = array();
	foreach ($tmpMethods as $method) {
		if (substr($method, 0, 2) != '__') // skip magic methods
			$vars['methods'][$system['name']][] = $method;
	}
}
		
		
// load steps
$vars['steps'] = StepFactory::fetchTree();

// run the template
require ('tmpl'.DS.'configure.php');



