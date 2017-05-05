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
				$profile = new Profile(null, $profileActivationToken, $requestObject->profileAtHandle, $requestObject->profileEMmail, $hash, $requestObject->profilePhone, $salt);

				//insert the profile into the database
				$profile->insert($pdo);

				//compose the email message to send the activation token
				$messageSubject = "one step closer to sticky head -- account activation";

				//building the activation link that can travel to another server and still work. This is the link that will be clicked to confirm the account
				//make sure URL is /public_html/api/activation/$activation
				$basePath = dirname($_SERVER["SCRIPT_NAME"], 3);

				//create the redirect link
				$confirmLink = "https://" . $_SERVER["SERVER_NAME"] . $urlglue;

				//compose message to send with email
				$message = <<< EOF
<h2>Welcome to DDCProduct.(/h2>
<p>In order to start posting products of dogs you must confirm your account</p>
<p><a href="$confirmLink">$confirmLink</a> </p>
EOF;

				//create swift email
				$swiftMessage = Swift_Message::newInstance();

				//attach the sender to the message
				//this takes the form of an associative array where the email is the key to a real name
				$swiftMessage->setFrom(["llopez165@cnm.edu" => "RoLopez"]);

				/**
				 * attach recipients to the message
				 * notice this is an array that can include or omit the recipients name
				 * use the recipients real name where possible;
				 * this reduces the probablilty of the email is marked as spamm
				 */

				//define who the recipient is
				$recipients = [$requestObject->profileEmail];

				//set the recipient to the swift message
				$swiftMessage->setTo($recipients);

				//attach the subject line to the email message
				$swiftMessage->setSubject($messageSubject);

				//attach the subject line to the email message
				$swiftMessage->setSubject($messageSubject);

				/**
				 * attach the message to the email
				 * set two versions of the message: a html formatted and a filter_var()ed version of the message, plain text
				 * notice the tactic used is to display the entire $confirmLink to plain text
				 * this lets user who ar not viewing the html content to still access the link
				 */

				//attach the html version for the message
				$swiftMessage->setBody($message, "text/html");


				//attach the plain text version of the message
				$swiftMessage->addPart(html_entity_decode($message), "text/plain");




			}
}