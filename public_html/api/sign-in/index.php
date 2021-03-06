<?php
/**
 * Created by PhpStorm.
 * User: LoroloDeuces
 * Date: 5/4/2017
 * Time: 11:28 PM
 */

require_once dirname(_DIR_,3) . "/phpclasses/autoload.php";
require_once dirname(_DIR_, 3) . "/php/lib/xsrf.php";
require_once ("/etc/apache2/capstone-mysql/encrypted-config.php");
use Edu\Cnm\DataDesign\Profile;


/**
 * api for handling sign in
 * @author Gkephart and @RoLopez
 */

//prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
	//start session
	if(session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	//grab mySQL statement
	$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/ddctwitter.ini");

	//determine which HTTP method is being used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_server) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	//if method is post handle the sign in logic
	if($method === POST) {

		//make sure the XSRF token is valid
		verifyXsrf();

		//process the request content and decode the json object into a php object
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		//check to make sure the password and email field is not empty
		if(empty($requestObject->profileEmail) === true) {
			throw(new \InvalidArgumentException("Wrong email address.", 401));

		} else {
					$profileEmail = filter_var($requestObject->profileEmail, FILTER_SANITIZE_EMAIL);
		}

		if(empty($requestObject->profilePassword) === true) {
					throw(new \InvalidArgumentException("Must enter a password.", 401));

		} else {
					$profilePassword = $requestObject->profilePassword;
		}

		//grab the profile from the database by the email provided
		$profile = Profile::getProfileByProfileEmail($pdo, $profileEmail);
		if(empty($profile) === true) {
					throw(new \InvalidArgumentException("Invalid Email", 401));
		}

		//if the profile activation is not null throw an error
		if($profile->getProfileActivationToken() !== null) {
					throw (new \InvalidArgumentException ("you are not allowed to sign in unless you have activated your account", 403));
		}

		//hash the password given to make sure it matches
		$hash = hash_pbkdf2("sha512", $profilePassword, $profile->getProfileSalt(), 262144);

		//verify hash is correct
		if($hash !== $profile->getProfileHash()) {
					throw(new \InvalidArgumentException("Password or email is incorrect"));
		}

		//grab profile from database and put into a session
		$profile = Profile::getProfileByProfile($pdo, $profile->getProfileId());
		$_SESSION["profile"] = $profile;
		$reply->message = "Sign in was successful.";

	} else {
		throw(new \InvalidArgumentException("Invalid HTTP method request"));
	}

	//if an exception is thrown update the
} catch(Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();

} catch(TypeError $typeError) {
	$reply->status = $typeError->getCode();
	$reply->message = $typeError->getMessage();
}

header("Content-type: application/json");
echo json_encode($reply);