<?php

require_once dirname(_DIR_, 3) . "/vendor/autoload.php";
require_once  dirname(_DIR_, 3) . "/php/calsses/autolaod.php";
require_once dirname(_DIR_, 3) . "php/lib/xsrf.php";
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");


use Edu\Cnm\DataDesign\ {
	Profile,
	Favorite
};

/**
 * *Api for the Favorite class
 * @author george kephart
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
		$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/ddctwitter.ini");

		//mock a logged in user by mocking the session and assigning a specific user to it
		//this is only for testing purposes and should not be in the live code
		$_SESSION["profile"] = Profile::getProfileByProfileId($pdo, 732);

		//determine which http method was used
		$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

		var_dump($method);

		//sanitize the search parameters
		$favoriteProfileId = filter_input(INPUT_GET, "FavoriteProifileId", FILTER_VALIDATE_INT);
		$favoriteProductId = filter_input(INPUT_GET, "favoriteProductId", FILTER_VALIDATE_INT);

		var_dump($favoriteProfileId);
		var_dump($favoriteProductId);

		if($method === "GET") {
			//set XSRF cookie
			setXsrfCookie();

			//gets a specific favorite associated based on its composite key
			if ($favoriteProfileId !==  null && $favoriteProductId !== null) {
				$favorite = Favorite::getFavoriteByFavoriteProductIdAndFavoriteProfileId($pdo, $favoriteProfileId, $favoriteProductId);


				if($favorite !== null) {
					$reply->data = $favorite;
				}

				//if none of the search parameters are met throw an exception
			} else if(empty($favoriteProfileId) === false) {
				$favorite = Favorite::getFavoriteByFavoriteProductId($pdo, $favoriteProductId)->toArray();

				if($favorite !== null) {
					$reply->data = $favorite;
				}

		} else {
				throw new InvalidArgumentException("incorrect search papramters", 404);
		}
} else if($method === "POST" || $method === "PUT") {

		//decode the response from the front end
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		if(empty($requestObject->favoriteProfileId) === true) {
				throw (new \InvalidArgumentException("No Profile linked to the favorite",405));
		}

		if(empty($requestObject->favoriteProductId) === true) {
				throw (new \InvalidArgumentException("No product linked to the Favorite", 405));
		}

		if(empty($requestObject->favoriteDate) === true) {
				$requestObject->favoriteDate = null;
		}

		if($method === "POST") {

				//enforce the user is signed in
				if(empty($_SESSION["profile"]) === true) {
						throw(new \InvalidArgumentException("you must be logged in too favorite posts", 403));
				}

				$favorite = new Favorite($requestObject->favoriteProfileId, $requestObject->favoriteProductId, $requestObject->favoriteDate);
				$favorite->insert($pdo);
				$reply->message = "favorited product successful";

		} else if($method === "PUT") {

				//enforce that the end user has a XSRF token
				verifyXsrf();

				//grab the favorite by its composite key
				$favorite = Favorite::getFavoriteByFavoriteProductIdAndFavoriteProfileId($pdo, $requestObject->favoriteProfileId, $requestObject->favoriteProductId);
				if($favorite === null) {
						throw (new RuntimeException("You are not allowed to delete the product"));
				}

				//preform the actual delete
				$favorite->delete($pdo);

				//update the message
				$reply->message = "Favorite successfully deleted";
			}

		} else {
			throw new \InvalidArgumentException("invalid http request, 400");
		}

		//catch any exceptions that is thrown and update the reply status and message
} catch(\Exception | \TypeError $exception) {
		$reply->status = $exception->getCode();
		$reply->message = $exception->getMessage();
}

header("Content-type: application/json");
if($reply->data === null) {
		unset($reply->data);
}

//encode and return reply to front end caller
echo json_encode($reply);