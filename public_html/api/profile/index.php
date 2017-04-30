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
if(session_start() !== PHP_SESSION_ACTIVE) {
	session_start();
}


//prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
	//grab mySQL connection
	//might be ddctwitter.ini instead of ddcproduct.ini
	$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/ddcproduct.ini");

	//mock a logged in user by moocking the session and assigning a specific user to it
	//this is only testing purpose and should not be in the live code
	//$_SESSION["profile"] = Profile::getProfileByProfileId($pdo, 732);

	//determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	//sanitize input
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
	$productProfileId = filter_input(INPUT_GET, "productProfileId", FILTER_VALIDATE_INT);
	$productContent = filter_input(INPUT_GET, "productContent", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

	//make sure the id is valid for methods that require it
	if(($method === "DELETE" || $method === "PUT") && (empty($id) === true || $id < 0)) {
		throw(new InvalidArgumentException("id can not be empty or negative", 405));
	}


//handle GET request - if id is present, that product is returned, otherwise all products are returned
	if($method === "GET") {
		//set XSRF cookie
		setXsrfCookie();

		//get a specific product of all products and update reply
		if(empty($id) === false) {
			$product = Product::getProductByProductId($pdo, $id);
			if($product !== null) {
				$reply->data = $product;
			}

		} else if(empty($productProfileId) === false) {
			$product = Product::getProductByProductProfileId($pdo, $productProfileId)->toArray();
			if($product !== null) {
				$reply->data = $product;
			}

		} else if(empty($productContent) === false) {
			$products = Product::getProductByProductContent($pdo, $productContent)->toArray();
			if($products !== null) {
				$reply->data = $product;
			}

		} else {
			$products = Product::getAllProducts($pdo)->toArray();
			if($products !== null) {
				$reply->data = $products;
			}
		}

	} else if($method === "PUT" || $method === "POST") {

		verifyXsrf();
		$requestContent = file_get_contents("php://input");
		// Retrieves the JSON package that the front end sent, and stores it in $requestContent. Here we are using file_get_contents("php://input") to get the request from the front end. file_get_contents() is a PHP function that reads a file into a string. The argument for the function, here, is "php://input". This is a read only stream that allows raw data to be read from the front end request which is, in this case, a JSON package.
		$requestObject = json_decode($requestContent);
		//This line then decodes the JSON package and stores the result in $requestObject

		//make sure product content is available (required field)
		if(empty($requestObject->productContent) === true) {
			throw(new \InvalidArgumentException("No content for product", 405));
		}

		//make sure product date is accurate
		if(empty($requestObject->productDate) === true) {
			$requestObject->productDate = null;
		}

		//make sure profileId is available
		if(empty($requestObject->productProfileId) === true) {
			throw(new \InvalidArgumentException ("No Profile Id.", 405));
		}

		//perform the actual put or post
		if($method === "PUT") {

			//enfore that the end user has a XSRF token
			verify();

			//retrieve the product to update
			$product = Product::getProductByProductId($pdo, $id);
			if($product === null) {
				throw(new RuntimeException("Product does not exist", 404));
			}

			//enforce the user is signed in and only tryin to edit their own product
			if(empty($_SESSION["profile"])=== true || $_SESSION["profile"]->getProfileId() !== $product->getProductId()) {
				throw(new \InvalidArgumentException("you are not allowed to edit this product", 403));
			}

			//update all attributes
			$product->setProductDate($requestObject->productDate);
			$product->setProductContent($requestObject->productContent);
			$product->update($pdo);

			//update reply
			$reply->message = "Product updated Ok";

		}else if($method === "POST") {

			//enforce that the end user has a XSRF token
			verifyXsrf();

			//enforce that the user is signed in
			if(empty($_SESSION["profile"]) === true) {
				throw(new \InvalidArgumentException("You must be logged in to post products", 403));
			}

			//create the new product and insert into the database
			$product = new Product(null, $requestObject->productProfileId, $requestObject->productContent, null);
			$product->message = "Product created Ok";
		}
	} else if($method === "DELETE") {

		//enforce that the end user has a XSRF token
		verifyXsrf();

		//retrieve the Product to be deleted
		$product = Product::getProductByProductId($pdo, $id);
		if($product === null) {
			throw(new RuntimeException("You are not allowed to deleted this produce", 403));
		}

		//delete product
		$product->delete($pdo);

		//update reply
		$reply->message = "Product deleted ok";

	}else {
		throw (new InvalidArgumentException("Invalid HTTP method request"));
	}

//update the $reply->status $reply->message
} catch(\Exception | \TypeError $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
}

header("Content-type: application/json");
if($reply->data === null) {
	unset($reply->data);
}

//encode and return reply to the front end caller
echo json_encode($reply);


