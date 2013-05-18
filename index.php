<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 *
 * This is the bootstrap script of Gitten which handles all page requests.
 * It is responsible for setting up the PHP environment and then starting
 * the Gitten application which then processes the request.
 */

// Define the Gitten version
define("GITTEN_VERSION", "1.0.0");

// Calculate base urls. BASEURL is for static resources, PHP_BASEURL is for
// PHP page requests. When mod_rewrite was detected then PHP_BASEURL is the
// same as the BASEURL. If no mod_rewrite was detected then PHP_BASEURL is
// this index.php bootstrap script.
define("BASEURL", dirname($_SERVER["SCRIPT_NAME"]));
if (isset($_SERVER["REDIRECT_URL"]))
    define("PHP_BASEURL", BASEURL);
else
    define("PHP_BASEURL", $_SERVER["SCRIPT_NAME"]);

// Load the Gitten configuration
require_once("lib/Gitten/Config.php");
$cfg = Gitten\Config::getInstance();

// Setup the include path so it includes the Gitten class library and the
// layout directory for reading the view templates
set_include_path(
    "lib" . PATH_SEPARATOR .
    "layouts/" . $cfg->getLayout() . PATH_SEPARATOR .
    get_include_path());

// Install class autoloader so we don't have to worry about including all the
// library class files we need. PHP can handle this automatically.
spl_autoload_register(function($className)
{
    $className = preg_replace("/\\\\/", "/", $className);
    return include_once "$className.php";
});

// Run the application
Gitten\Gitten::run();
