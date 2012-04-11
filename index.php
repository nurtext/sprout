<?php
// Include Smarty and Router class
require_once('libs/Smarty.class.php');
require_once('libs/Router.class.php');

// Instantiate singleton router class
$router = Router::getInstance();

// Instantiate Smarty
$router->setSmarty(new Smarty());

// These calls are optional and just in case you want to have 
// a different default route and/or filename style ending
//$router->setDefaultRoute('default');
//$router->setFilenameEnding('.html');

// You can also change the trigger variable, which overrides
// the $_GET parameter for the route. Don't forget to change
// the .htaccess if you're using mod_rewrite!
//$router->setTriggerVariable('foo');

// Let the routing begin...
$router->doRouting();