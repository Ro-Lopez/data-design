<?php

namespace Edu\Cnm\DataDesign;

require_once("autoload.php");

/**
 * cross section of a product favorite
 *
 *cross section of what probably happens when a user favorites a product, a weak table between Profile and Product
 *
 * @author RoLopez <llopez165@cnm.edu> but pretty much copied off of Dyalan Mcdonald <dmcdonald21@cnm.edu>
 */
//favoriteProfileId, favoriteProductId, favoriteDate

class Favorite implements \JsonSerializable {

	//using ValidateDate.php
	use ValidateDate;
	/**
	*id for favorite profile if, primary key
	*@var int $favoriteProfileId
	 **/
	private $favoriteProfileId;
	/**
	*id for favorite product id
	*@var string $favoriteProductId
	 **/
	private $favoriteProductId;

	/**
	*date and time the product was favorite
	*@var \datetime $favoriteDate
	 **/
	private $favoriteDate;


	/**
	 * constructor for this Favorite
	 * @param int $newFavoriteProfileId id of the parent Profile
	 * @param int $newFavoriteProductId id of the parent Product
	 * @param \DateTime|null $newFavoriteDate date the product was favorite (or null for current time)
	 * @throws \Exception if some other exception occurs
	 * @throws \TypeError if data types violate type hints
	 * @Documentation https://php.net/manual/en/language.oop5.decon.php
	 */
	public function _construct(int $newFavoriteProfileId, int $newFavoriteProductId, $newFavoriteDate = null) {
		try {
			$this->setFavoriteProfileId($newFavoriteProfileId);
			$this->setFavoriteProductId($newFavoriteProductId);
			$this->setFavoriteDate($newFavoriteDate);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			//determine what exception was thrown
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(),0, $exception));
		}
	}

	/**
	*accessor method for favorite profile id
	*@return int value for favorite profile id
	**/
	public function getFavoriteProfileId(): int {
		return ($this->favoriteProfileId);
	}

	/**
	 * mutator method for profile id
	 * @param int $newFavoriteProfileId new value of profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newProfileId is not an integer
	 **/
	public function setFavoriteProfileId(int $newFavoriteProfileId): void {
		if($newFavoriteProfileId <=0) {
			throw(new \RangeException("Profile id is not positive"));
		}

		//convert and store favorite profile id
		$this->favoriteProfileId = $newFavoriteProfileId;
	}

	/**
	*accessor method for favorite product id
	*@return int value of profile id
	**/
	public function getFavoriteProductId(): int {
		return ($this->favoriteProductId);
	}

	/**
	 * mutator method for product id
	 * @param int $newFavoriteProductId new value of tweet id
	 * @throws \RangeException if $newProductId is not positive
	 * @throws \TypeError if $newProductId is not an integer
	 **/
	public function setFavoriteProductId(int $newFavoriteProductId): void {
		//verify favorite product id is positive
		if($newFavoriteProductId <= 0) {
			throw (new \RangeException("product profile id is not positive"));
		}

		//convert and store profile
		$this->favoriteProductId = $newFavoriteProductId;
	}


	/**
	*accessor method for favoriteDate
	* @return \DateTime value of favorite date
	**/
	public function getFavoriteDate(): \DateTime {
		return ($this->favoriteDate);
	}

	//mutator method for product date
	public function setFavoriteDate($newFavoriteDate =  null): void {
		// if date is null, use current date and time
		if($newFavoriteDate === null) {
			$this->favoriteDate = new \DateTime();
			return;
		}

		//store the favorite date using the ValidateDate trait
		try {
			$newFavoriteDate = self::validateDateTime($newFavoriteDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		$this->favoriteDate = $newFavoriteDate;
	}

	/**
	 * inserts this Favorite into mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo) : void {
		//enforce the object exists before insert
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


	/**
	 * deletes this Favorite from mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {
		//ensure the object exists before deleting
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


	/**
	 * gets the Favorite by product id and profile id
	 * @param \PDO $pdo PDO connection object
	 * @param int $favoriteProfileId profile id to search for
	 * @param int $favoriteProductId tweet id to search for
	 * @return Favorite|null Favorite found or null if not found
	 */
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




	/**
	 * gets the Favorite by profile id
	 * @param \PDO $pdo PDO connection object
	 * @param int $favoriteProfileId profile id to search for
	 * @return \SplFixedArray SplFixedArray of Favorites found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getFavoriteByFavoriteProfileId(\PDO $pdo, int $favoriteProfileId) : \SplFixedArray {
		//sanitize the profile id
		if($favoriteProfileId <= 0) {
			throw(new \PDOException("profile id is not positive"));
		}

		//create query template
		$query = "SELECT favoriteProfileId, favoriteProductId, favoriteDate FROM 'favorite' WHERE favoriteProfileId = :favoriteProfileId";
		$statement = $pdo->prepare($query);

		//bind the member var to the place holders in the template
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


	/**
	 * gets the Favorite by favorite it id
	 * @param \PDO $pdo PDO connection object
	 * @param int $favoriteProductId product id to search for
	 * @return \SplFixedArray array of Favorites found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
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

	/**
	formats the state var for JSON serialization
	@return array resulting state var to serialize
	**/
	public function jsonSerialize() {
		$fields = get_object_vars($this);
		//format the date so that the front end can consume it
		$fields["favoriteDate"] = round(floatval($this->favoriteDate->format("U.u")) * 1000);
		return ($fields);
	}

}