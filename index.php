<?php
// index.php
//
// An implementation of a PHP frontcontroller for a web-site.
//
// All requests passes through this page, for each request a pagecontroller is choosen.
// The pagecontroller results in a response or a redirect.
//
// Require the files and actions that are common for all modules and pagecontrollers.
session_start();
error_reporting(E_ALL);

// Get global config-files with template structure
require_once('config-global.php');

// Enable autoload for classes
function __autoload($class_name) { require_once(TP_SOURCEPATH . $class_name . '.php'); }

// Redirect to the choosen modulecontroller (if a module is defined). Review modules.php
// for further details.
global $gModulesAvailable; // Set in config-global.php

// Get the requested page- and module id.
$gModule 	= isset($_GET['m']) ? $_GET['m'] : 'core';
$gPage 		= isset($_GET['p']) ? $_GET['p'] : 'home';


// Check if the choosen module is available, if not show 404
if(!array_key_exists($gModule, $gModulesAvailable)) {
	require_once('config.php');
	require_once(TP_PAGESPATH . 'home/P404.php');
	exit;
}


// Load the module config-page, if it exists. Else load default config.php
$configFile = $gModulesAvailable["{$gModule}"] . '/config.php';

if(is_readable($configFile)) {
	require_once($configFile);
} else {
	require_once('config.php');
}


// Start a timer to time the generation of this request
if(WS_TIMER) { $gTimerStart = microtime(TRUE); }


// Redirect to module controller.
$moduleController = $gModulesAvailable["{$gModule}"] . '/index.php';

if(is_readable($moduleController)) {
	require_once($moduleController);
} else {
	require_once(TP_PAGESPATH . 'home/P404.php');
}
?>