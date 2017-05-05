<?php
/**
 * Created by PhpStorm.
 * User: LoroloDeuces
 * Date: 5/5/2017
 * Time: 12:51 AM
 */

require_once dirname(_DIR_, 3) .  "/vendor/autoload.php";
require_once dirname(_DIR_, 3) . "php/classes/autoload.php";
require_once dirname(_DIR_, 3) . "/php/lib/xsrf.php";
require_once ("/etc/apache2/capstone-mysql/encrypted-config.php");
use Edu\Cnm\DataDesign\Profile;


/**
 * api for signing up too DDC product
 * @author Gkephart <GKephart@cnm.edu>
 */

//verify the session, start if not active
if(session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
}

//prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
			//grab the mySQL connection
			$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/ddctwitter.ini");
			//determine which HTTP method

			$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER['HTTP_X_HTTP_METHOD'] : $_SERVER["REQUEST_METHOD"];

			if($method === "POST") {

				//decode the json and turn it into a php object
				$requestContent = file_get_contents("php://input");
				$requestObject = json_encode($requestContent);

				//profile at handle is required field
				if(empty($requestObject->profileAtHandle) === true) {
							throw(new \InvalidArgumentException("No profile @handle", 405));
				}

				//profile email is a required field
				if(empty($requestObject->profileEmail) === true) {
							throw(new \InvalidArgumentException("No profile email present", 405));
				}

				//verify that profile password is present
				if(empty($requestObject->profilePassword) === true) {
							throw(new \InvalidArgumentException ("Must input valid password", 405));
				}

				//verify that the confirm password is present
				if(empty($requestObject->profilePassword) === true) {
							throw(new \InvalidArgumentException("Must input valid confirmed password", 405));
				}

				//if phone is empty set it to null
				if(empty($requestObject->profilePhone) === true) {
							$requestObject->profilePhone = null;
				}

				//make sure the password and confirm password match
				if ($requestObject->profilePassword !== $requestObject->profilePasswordConfirm) {
							throw(new \InvalidArgumentException("passwords do not match"));
				}

				$salt = bin2hex(random_bytes(16));
				$hash = hash_pbkdf2("sha512", $requestObject->profilePassword, $salt, 262144);

				$profileActivationToken = bin2hex(random_bytes(16));

				//create the profile object and prepare t insert into the database
			}
}