<?php

//Define the core paths
//Define them as absolute paths to make sure that require_once
//works as expected

//DIRECTORY_SEPARATOR is a PHP pre-defined constant
//(\ for windows, / for UNIX)

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', DS.'wamp64'.DS.'www'.DS.'agentman');

defined('INC_PATH') ? null : define('INC_PATH', SITE_ROOT.DS.'includes');
defined('CORE_PATH') ? null : define('CORE_PATH', SITE_ROOT.DS.'core');


//load config file first
require_once(CORE_PATH.DS."config.php");
require_once(CORE_PATH.DS."functions.php");
require_once(CORE_PATH.DS."session.php");
