<?php

namespace Edu\Cnm\DataDesign;

require_once("autoload.php");

//Favorite a product through a profile

//@author rolopez <llopez165@cnm.edu> - pretty much copied from @deepdivedylan (non ctrl c and ctrl v practices)


//setting up classes
class Product implements \JsonSerializable {

	//using ValidateDate.php
	use ValidateDate;

	/**
	*id for this product
	 *@var int $productId
	 * **/
	private $productId;

	/**
	*id of the Profile that favorite this product, foreign key
	*@var int $productProfileId
	 **/
	private $productProfileId;

	/**
	*content of the product
	*@var string $productContent
	 **/
	private $productContent;

	/**
	*date and time this product was favorite
	*@var \Datetime $productDate
	 **/
	private $productDate;

	/**
	 * constructor for this Product
	 * @param int|null $newProductId id of this Product or null if a new Product
	 * @param int $newProductProfileId id of the Profile that sent this Product
	 * @param string $newProductContent string containing actual product data
	 * @param \DateTime|string|null $newProductDate date and time Product was sent or null if set to current date and time
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 **/
	public function _construct(?int $newProductId, int $newProductProfileId, string $newProductContent, $newProductDate = null) {
		try {
			$this->setProductId($newProductId);
			$this->setProductProfileId($newProductProfileId);
			$this->setProductContent($newProductContent);
			$this->setProductDate($newProductDate);
		}
			//what exception was thrown
			// "|" means "or"
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	/**
	*accessor method for product id
	*@int|null value of product id
	**/
	public function getProductId(): ?int {
		return ($this->productId);
	}

	/**
	 * mutator method for product id
	 * @param int|null $newProductId new value of product id
	 * @throws \RangeException if $newProductId is not positive
	 * @throws \TypeError if $newProductId is not an integer
	 **/
	public function setProductId(?int $newProductId): void {
		if($newProductId === null) {
			$this->productId = null;
			return;
			//null represents a variable with no value
		}
		//verify product id is positive
		if($newProductId <= 0) {
			throw(new \RangeException("product id is not positive"));
		}
		//convert and store product id
		$this->productId = $newProductId;
	}


	/**
	*@return int value of the product profile
	*accessor method for product profile id
	 **/
	public function getProductProfileId(): int {
		return ($this->productProfileId);
	}

	/**
	 * mutator method for product profile id
	 * @param int $newProductProfileId new value of product profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newProfileId is not an integer
	 **/
	public function setProductProfileId(int $newProductProfileId): void {
		//verify profile id is positive
		if($newProductProfileId <= 0) {
			throw (new \RangeException("product profile id is not positive"));
		}
		//convert and store profile id
		$this->productProfileId = $newProductProfileId;
	}


	/**
	*accessor method for product content
	*@return string value of product content
	**/
	public function getProductContent(): string {
		return ($this->productContent);
	}

	/**
	 * mutator method for product content
	 * @param string $newProductContent new value of product content
	 * @throws \InvalidArgumentException if $newProductContent is not a string or insecure
	 * @throws \RangeException if $newProductContent is > 140 characters
	 * @throws \TypeError if $newTProductContent is not a string
	 **/
	public function setProductContent(string $newProductContent): void {
		//verify product content is secure
		$newProductContent = trim($newProductContent);
		$newProductContent = filter_var($newProductContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newProductContent) === true) {
			throw(new \InvalidArgumentException("product content is empty or insecure"));
		}
		//verify product content will fit in the database
		//140 characters is a twitter thing, probably can use own number
		if(strlen($newProductContent) > 140) {
			throw(new \RangeException("product content too large"));
		}
		//store the product content
		$this->productContent = $newProductContent;
	}

	/**
	* accessor method for product date
	* @return \Datetime val of product date
	 **/
	public function getProductDate(): \DateTime {
		return ($this->productDate);
	}

	/**
	 * mutator method for product date
	 * @param \DateTime|string|null $newProductDate product date as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newProductDate is not a valid object or string
	 * @throws \RangeException if $newProductDate is a date that does not exist
	 **/
	public function setProductDate($newProductDate = null): void {
		//if date is null, use current date and time
		if($newProductDate === null) {
			$this->productDate = new \DateTime();
			return;
		}
		//store the product date using the ValidateDate trait
		try {
			$newProductDate = self::validateDateTime($newProductDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->productDate = $newProductDate;
	}


	/**
	 * inserts this Product into mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo): void {
		//enforce the productId is null, do not insert a tweet that already exists
		if($this->productId !== null) {
			throw(new \PDOException("not a new product"));
		}
		//create query template
		$query = "INSERT INTO product(productProfileId, productContent, productDate) VALUES(:productProfileId, :productContent, :productDate)";
		$statement = $pdo->prepare($query);

		//bind the member variables to the places holders in the template
		$formattedDate = $this->productDate->format("Y-m-d H:i:s.u");
		$parameters = ["productProfileId" => $this->productProfileId, "productContent" => $this->productContent, "productDate" => $formattedDate];
		$statement->execute($parameters);

		//update the null productId with what mySQL just gave us
		$this->productId = intval($pdo->lastInsertId());
	}


	/**
	 * deletes this Product from mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo): void {

		//enforce the productId is not null, do not delete product that has not been inserted
		if($this->productId === null) {
			throw(new \PDOException("unable to delete a product that does not exist"));
		}

		//create query template
		$query = "DELETE FROM product WHERE productId = :productId";
		$statement = $pdo->prepare($query);

		//bind the member variables to the place holder in the template
		$parameters = ["productId" => $this->productId];
		$statement->execute($parameters);
	}


	/**
	 * updates this Product in mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo): void {
		//enforce productId is not null, do not update a tweet that has not been inserted
		if($this->productId === null) {
			throw(new \PDOException("unable to update a product that does not exist"));
		}

		//create query template
		$query = "UPDATE product SET productProfileId = :productProfileId, productContent = :productContent, productDate = :productDate WHERE productId = :productId";
		$statement = $pdo->prepare($query);

		//bind the member variables to the place holders in the template
		$formattedDate = $this->productDate->format("Y-m-d H:i:s.u");
		$parameters = ["productProfileId" => $this->productProfileId, "productContent" => $this->productContent, "productDate" => $formattedDate, "productId" => $this->productId];
		$statement->execute($parameters);
	}

	/**
	 * gets the Product by productId
	 * @param \PDO $pdo PDO connection object
	 * @param int $productId product id to search for
	 * @return Product|null Product found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
		public static function getProductByProductId(\PDO $pdo, int $productId) : ?Product {
			//sanitize tht productId before searching
			if($productId <= 0) {
				throw(new \PDOException("product id is not positive"));
			}

			//create query template
			$query = "SELECT productId, productProfileId, productContent, productDate FROM product WHERE productId = :productId";
			$statement = $pdo->prepare($query);

			//bind the product id to the places holder in the template
			$parameters = ["productId" => $productId];
			$statement->execute($parameters);

			//grab the product from mySQL
			try {
					$product = null;
					$statement->setFetchMode(\PDO::FETCH_ASSOC);
					$row = $statement->fetch();
					if($row !== false) {
						$product = new Product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
					}

			}catch(\Exception $exception) {
				//if the row could not be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
			return($product);
		}



	/**
	 * gets the Product by profile id
	 * @param \PDO $pdo PDO connection object
	 * @param int $productProfileId profile id to search by
	 * @return \SplFixedArray SplFixedArray of Products found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
		public static function getProductByProductProfileId(\PDO $pdo, int $productProfileId) : \SplFixedArray {
			//sanitize the profile id before searching
			if($productProfileId <= 0) {
				throw(new \RangeException("product profile id must be positive"));
			}

			//create query template
			$query = "SELECT productId, productProfileId, productContent, productDate FROM product WHERE productProfileId = :productProfileId";
			$statement = $pdo->prepare($query);

			//bind the product profile if to the place holder in the template
			$parameters = ["productProfileId" => $productProfileId];
			$statement->execute($parameters);

			//build an array of products
			$products = new \SplFixedArray($statement->rowCount());
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			while(($row = $statement->fetch()) !== false) {
				try {
						$product = new Product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
						$products[$products->key()] = $product;
						$products->next();
				} catch(\Exception $exception) {
					//if the row could not be converted, rethrow it
					throw(new \PDOException($exception->getMessage(), 0, $exception));
				}
			}
			return($products);
		}



	/**
	 * gets the Product by content
	 * @param \PDO $pdo PDO connection object
	 * @param string $productContent product content to search for
	 * @return \SplFixedArray SplFixedArray of Products found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
		public static function getProductsByProductContent(\PDO $pdo, string $productContent) : \SplFixedArray {
			//sanitize the description before searching
			$productContent = trim($productContent);
			$productContent = filter_var($productContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			if(empty($productContent) === true) {
				throw(new \PDOException("product content is invalid"));
			}

			//create query template
			$query = "SELECT productId, productProfileId, productContent, productDate FROM product WHERE productContent LIKE :productContent";
			$statement = $pdo->prepare($query);

			//bind the product content to the place holder in the template
			$productContent = "%$productContent%";
			$parameters = ["productContent" => $productContent];
			$statement->execute($parameters);

			//build an  array of products
			$products = new \SplFixedArray($statement->rowCount());
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			while(($row = $statement->fetch()) !== false) {
				try {
						$product = new Product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
						$products[$products->key()] = $product;
						$products->next();
				} catch(\Exception $exception) {
					//if the row could not be converted, rethrow it
					throw(new \PDOException($exception->getMessage(), 0, $exception));
				}
			}
			return($products);
		}


	/**
	 * gets an array of products based on its site
	 * optional get by method and only added on certain cases
	 * @param \PDO $pdo connection object
	 * @param \DateTime $sunriseProductDate beginning date to search for
	 * @param \Datetime $sunsetProductDate ending date to search for
	 * @return \SplFixedArray of products found
	 * @throws \PDOException wheh mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 * @throws \InvalidArgumentException if either sun dates are in the wrong format
	 */
	public static function getProductByProductDate (\PDO $pdo, \DateTime $sunriseProductDate, \DateTime $sunsetProductDate ) : \SplFixedArray {
		//enforce both date are present
		if((empty ($sunriseProductDate) === true) || (empty($sunsetProductDate) === true)) {
			throw (new \InvalidArgumentException("dates are empty of insecure"));
		}

		//ensure both dates are in the correct format and are secure
		try {
				$sunriseProductDate = self::validateDateTime($sunriseProductDate);
				$sunsetProductDate = self::validateDateTime($sunsetProductDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		//create query template
		$query = "SELECT productId, productProfileId, productContent, productDate from product WHERE productDate >= :sunriseProductDate AND productDate <= :sunsetProductDate";
		$statement = $pdo->prepare($query);

		//format the dates so that ,ySQL can use them
		$formattedSunriseDate = $sunriseProductDate->format("Y-m-d H:i:s.u");
		$formattedSunsetDate = $sunsetProductDate->format("Y-m-d H:i:s.u");

		$parameters = ["$sunriseProductDate" => $formattedSunriseDate, "sunsetProductDate" => $formattedSunsetDate];
		$statement->execute($parameters);

		//build an array of products
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
					$product = new Product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
					$products[$products->key()] = $product;
					$products->next();
			} catch(\Exception $exception) {
					throw (new \PDOException($exception->getCode(), 0, $exception));
			}
		}
		return($products);

	}



	/**
	 * gets all Products
	 * @param \PDO $pdo PDO connection object
	 * @return \SplFixedArray SplFixedArray of Products found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
		public static function getAllProducts(\PDO $pdo) : \SplFixedArray {
			//create query template
			$query = "SELECT productId, productProfileId, productContent, productDate FROM product";
			$statement = $pdo->prepare($query);
			$statement->execute();

			//build an array of products
			$products = new \SplFixedArray($statement->rowCount());
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			while(($row = $statement->fetch()) !== false) {
				try {
						$product = new Product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
						$products[$products->key()] = $product;
						$products->next();
				} catch(\Exception $exception) {
					//if the row could not be converted, rethrow it
					throw(new \PDOException($exception->getMessage(), 0, $exception));
				}
			}
			return ($products);
		}


		/**
		//formats the state variables for JSON serialization
		//@return array resulting state variables to serialize
		**/
		public function jsonSerialize() {
			$fields = get_object_vars($this);
			//format the date so that the front end can consume it
			$fields["productDate"] = round(floatval($this->productDate->format("U.u")) * 1000);
			return($fields);
		}
}

