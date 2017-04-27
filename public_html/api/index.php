<?php

require_once dirname(__DIR__, 3) . "/vendor/autoload.php";
require_once dirname(__DIR__, 3 ) . "/php/classes/autoload.php";
require_once dirname(__DIR__, 3) . "/php/lib/xsrf.php";
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");


use Edu\Cnm\DataDesign\{
	Product,
	//only using profile class for testing
	Profile
};


/**
 * api for Product class
 * @author Valente Meza <valebmeza@gmail.com> and @deepdivedylan
 */

//verify the session, start if not active
if(session_start() !== PHP_SESSION_ACTIVE)