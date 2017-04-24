<?php

namespace Edu\Cnm\DataDesign;

require_once("autoload.php");

//favoriteProfileId, favoriteProductId, favoriteDate

class Favorite implements \JsonSerializable {

	//using ValidateDate.php
	use ValidateDate;

	//id for favorite profile if, primary key
	//@var int $favoriteProfileId
	private $favoriteProfileId;

	//id for favorite product id
	//@var string $favoriteProductId
	private $favoriteProductId;

	//date and time the product was favorite
	//@var \datetime $favoriteDate
	private $favoriteDate;


	//constructor for this profile
	public function _construct(?int $newfavoriteProfileId, ?int $newfavoriteProductId, $newfavoriteDate = null) {
		try {
			$this->setfavoriteProfileId($newfavoriteProfileId);
			$this->setfavoriteProductId($newfavoriteProductId);
			$this->setfavoriteDate($newfavoriteDate);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			//determine what exception was thrown
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(),0, $exception));
		}
	}


	//accessor method for favorite profile id
	//@return int value for favorite profile id
	public function getfavoriteProfileId(): int {
		return ($this->favoriteProfileId);
	}

	//this look different then whats in the example
	//mutator method for favorite profile id
	public function setfavoriteProfileId(?int $newfavoriteProfileId): void {
		if($newfavoriteProfileId === null) {
			$this->favoriteProfileId = null;
			return;
		}

		//verify the favorite profile id is positive
		if($newfavoriteProfileId <= 0) {
			throw(new \RangeException("Profile id is not positive"));
		}

		//convert and store favorite profile id
		$this->favoriteProfileId = $newfavoriteProfileId;
	}


	//accessor method for favorite product id
	public function getfavoriteProductId(): int {
		return ($this->favoriteProductId);
	}

	//mutator method for favorite product id
	public function setfavoriteProductId(int $newfavoriteProductId): void {
		//verify favorite product id is positive
		if($newfavoriteProductId <= 0) {
			throw (new \RangeException("product profile id is not positive"));
		}

		//convert and store profile
		$this->favoriteProductId = $newfavoriteProductId;
	}


	//accessor method for favoriteDate
	public function getfavoriteDate(): \DateTime {
		return ($this->favoriteDate);
	}

	//mutator method for product date
	public function setfavoriteDate($newfavoriteDate =  null): void {
		// if date is null, use current date and time
		if($newfavoriteDate === null) {
			$this->favoriteDate = new \DateTime();
			return;
		}

		//store the favorite date using the ValidateDate trait
		try {
			$newfavoriteDate = self::validateDateTime($newfavoriteDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		$this->favoriteDate = $newfavoriteDate;
	}


	//inserts this Favorite into mySQL
	public function insert(\PDO $pdo) : void {
		//enforce the objest exists before inserting
		if($this->favoriteProfileId === null || $this->favoriteProductId === null) {
			throw(new \PDOException("not a new favorite"));
		}

		//create query template
		$query = "INSERT INTO 'favorite'(favoriteProfileId, favoriteProductId, favoriteDate) VALUES(:favoriteProfileId, :favoriteProductId, :favoriteDate)";
		$statement = $pdo->prepare($query);

		//bind the member variables to the place holders in the template
		$formattedDate = $this->favoriteDate->format("Y-M-d H:i:s");
		$parameters = ["favoriteProfileId" => $this->favoriteProfileId, "favoriteProductId" => $this->favoriteProductId, "favoriteDate" => $formattedDate];
		$statement->execute($parameters);
	}


	//deletes this Favorite from mySQL
	public function delete(\PDO $pdo) : void {
		//enusre the object exists before deleting
		if($this->favoriteProfileId === null || $this->favoriteProductId === null) {
			throw(new \PDOException("not a vaild favorite"));
		}

		//create query template
		$query = "DELETE FROM 'favorite' WHERE favoriteProfileId = :favoriteProfileId AND favoriteProductId = :favoriteProductId";
		$statement = $pdo->prepare($query);

		//bind the member var to the place holders in the template
		$parameters = ["favoriteProfileId" => $this->favoriteProfileId, "favoriteProductId" => $this->favoriteProductId];
		$statement->execute($parameters);
	}


	//gets the Favorite by product id and profile id
	public static function getFavoriteByFavoriteProductIdAndFavoriteProfileId(\PDO $pdo, int $favoriteProfileId, int $favoriteProductId) : ?Favorite {
		//sanitize the product id and profile id before searching
		if($favoriteProfileId <= 0) {
			throw(new \PDOException("profile id is not positive"));
		}

		if($favoriteProductId <= 0) {
			throw(new \PDOException("product id is not positive"));
		}

		//create query template
		$query = "SELECT favoriteProfileId, favoriteProductId, favoriteDate FROM 'favorite' WHERE favoriteProfileId = :favoriteProfileId AND favoriteProductId = :favoriteProductId";
		$statement = $pdo->prepare($query);

		//bind the product id and profile id to the place holder in the template
		$parameters = ["favoriteProfileId" => $favoriteProfileId, "favoriteProductId" => $favoriteProductId];
		$statement->execute($parameters);

		//grab the favorite from mySQL
		try {
				$favorite = null;
				$statement->setFetchMode(\PDO::FETCH_ASSOC);
				$row = $statement->fetch();
				if($row !== false) {
					$favorite = new Favorite($row["favoriteProfileId"], $row["favoriteProductId"], $row["favoriteDate"]);
				}
		} catch(\Exception $exception) {
			//if the row could not be converted, rethrow it
			throw(new \PDOexception($exception->getMessage(), 0, $exception));
	}
	return ($favorite);
	}



	//gets the Favorite by profile id
	public static function getFavoriteByFavoriteProfileId(\PDO $pdo, int $favoriteProfileId) : \SplFixedArray {
		//sanitize the profile id
		if($favoriteProfileId <= 0) {
			throw(new \PDOException("profile id is not positive"));
		}

		//create query template
		$query = "SELECT favoriteProfileId, favoriteProductId, favoriteDate FROM 'favorite' WHERE favoriteProfileId = :favoriteProfileId";
		$statement = $pdo->prepare($query);

		//bind the member var to the place holderss in the template
		$parameters = ["favoriteProfileId" => $favoriteProfileId];
		$statement->execute($parameters);

		//build an array of favorites
		$favorites = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
					$favorite = new Favorite($row["favoriteProfileId"], $row["favoriteProductId"], $row["favoriteDate"]);
					$favorites[$favorites->key()] = $favorite;
					$favorites->next();
			} catch(\Exception $exception) {
				//if the row could not be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($favorites);
	}


	//gets the Favorite by product id
	public static function getFavoriteByFavoriteProductId(\PDO $pdo, int $favoriteProductId) : \SplFixedArray {
		//sanitize the product id
		$favoriteProductId = filter_var($favoriteProductId,FILTER_VALIDATE_INT);
		if($favoriteProductId <= 0) {
			throw(new \PDOException("product id is not positive"));
		}

		//create query template
		$query = "SELECT favoriteProfileId, favoriteProductId, favoriteDate FROM 'favorite' WHERE favoriteProductId = :favoriteProductId";
		$statement = $pdo->prepare($query);

		//bind the member var to the place holders in the template
		$parameters = ["favoriteProductId" => $favoriteProductId];
		$statement->execute($parameters);

		//build the array of favorites
		$favorites = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
					$favorite = new Favorite($row["favoriteProfileId"], $row["favoriteProductId"], $row["favoriteDate"]);
					$favorites[$favorites->key()] = $favorite;
					$favorites->next();
			} catch(\Exception $exception) {
				//if the row could not be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($favorites);
	}


	//formats the state var for JSON serialization
	//@return array resulting state var to serialize
	public function jsonSerialize() {
		$fields = get_object_vars($this);
		//format the date so that the front end can consume it
		$fields["favoriteDate"] = round(floatval($this->favoriteDate->format("U.u")) * 1000);
		return ($fields);
	}

}