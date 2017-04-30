<?php

namespace Edu\Cnm\DataDesign;

require_once("autoload.php");

//	cross section of a product profile

//this is the section for the product profile

//@author rolopez <llopez165@cnm.edu> - pretty much copied from @deepdivedylan (non ctrl c and ctrl v practices bro)


class Profile implements \JsonSerializable {

	use ValidateDate;

	/**
	*id for this Profile; primary key
	*@var int $profileId
	 *
	 **/
	private $profileId;

	/**
	*at handle for this Profile; this is a unique index
	*@var string $profileAtHandle
	**/
	private $profileAtHandle;

	/**
	*token (usually an email?) handed out to verify that profile is valid
	*@var $profileActivationToken;
	**/
	private $profileActivationToken;

	/**
	*email for this Profile; this is a unique index
	*@var string $profileEmail
	**/
	private $profileEmail;

	/**
	*hash for profile pw
	*@var $profileHash
	**/
	private $profileHash;

	/**
	*phone number for this Profile
	*@var string $profilePhone
	**/
	private $profilePhone;

	/**
	*salt for this profile password
	/*@var $profileSalt
	**/
	private $profileSalt;

	/**
		constructor for this profile
	 * @param int|null $newProfileId id of this Profile or null if a new Profile
	 * @param string $newProfileActivationToken activation token to safe guard against malicious accounts
	 * @param string $newProfileAtHandle string containing newAtHandle
	 * @param string $newProfileEmail string containing email
	 * @param string $newProfileHash string containing password hash
	 * @param string $newProfilePhone string containing phone number
	 * @param string $newProfileSalt string containing password salt
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception
	 **/

	//what is ?string
	public function _construct(?int $newProfileId, ?string $newProfileActivationToken, ?string $newProfileAtHandle, string $newProfileEmail, string $newProfileHash, ?string $newProfilePhone, string $newProfileSalt) {
		try {
				$this->setProfileId($newProfileId);
				$this->setProfileActivationToken($newProfileActivationToken);
				$this->setProfileAtHandle($newProfileAtHandle);
				$this->setProfileEmail($newProfileEmail);
				$this->setProfileHash($newProfileHash);
				$this->setProfilePhone($newProfilePhone);
				$this->setProfileSalt($newProfileSalt);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			//determine what exception type was thrown
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	/**
	*accessor method for profile id
	*@return int value of profile id (or null if new profile)
	**/
	 public function getProfileId() {
		return ($this->profileId);
	}

	/**
	 * mutator method for profile id
	 * @param int|null $newProfileId value of new profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newProfileId is not an integer
	 **/
	public function setProfileId(?int $newProfileId): void {
		if($newProfileId === null)  {
			$this->profileId = null;
			return;
		}

		//verify the profile Id is positive
		if($newProfileId <= 0) {
			throw(new \RangeException("profile id is not positive"));
		}
		//convert and store the profile id
		$this->profileId = $newProfileId;
	}


	/**
	*accessor method for account activation token
	*@return mixed - string value of the activation token
	**/
	public function getProfileActivationToken() {
		return ($this->profileActivationToken);
	}

	/**
	 * mutator method for account activation token
	 * @param string $newProfileActivationToken
	 * @throws \InvalidArgumentException  if the token is not a string or insecure
	 * @throws \RangeException if the token is not exactly 32 characters
	 * @throws \TypeError if the activation token is not a string
	 */
	public function setProfileActivationToken(?string $newProfileActivationToken): void {
		if($newProfileActivationToken === null) {
			$this->profileActivationToken = null;
			return;
		}

		$newProfileActivationToken = strtolower(trim($newProfileActivationToken));
		if(strlen($newProfileActivationToken) !== 32) {
			throw(new\RangeException("user activation token has to be 32 characters"));
		}
		$this->profileActivationToken = $newProfileActivationToken;
	}

	/**
	*accessor method for at handle
	*@return mixed - string value for handle
	**/
	 public function getProfileAtHandle(): string {
		return $this->profileAtHandle;
	}

	/**
	 * mutator method for at handle
	 * @param string $newProfileAtHandle new value of at handle
	 * @throws \InvalidArgumentException if $newAtHandle is not a string or insecure
	 * @throws \RangeException if $newAtHandle is > 32 characters
	 * @throws \TypeError if $newAtHandle is not a string
	 **/
	public function setProfileAtHandle(string $newProfileAtHandle) : void {
		//verify at handles is secure
		$newProfileAtHandle = trim($newProfileAtHandle);
		$newProfileAtHandle = filter_var($newProfileAtHandle, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newProfileAtHandle) === true) {
			throw(new \InvalidArgumentException("profile at handle is empty or insecure"));
		}

		//verify the at handle will fir in the database
		if(strlen($newProfileAtHandle) > 32) {
			throw(new \RangeException("profile at handle is too large"));
		}

		//store the at handle
		$this->profileAtHandle = $newProfileAtHandle;
	}

	/**
	*accessor method for email
	*@return mixed string value of email
	**/
	 public function getProfileEmail(): string {
		return $this->profileEmail;
	}

	/**
	 * mutator method for email
	 * @param string $newProfileEmail new value of email
	 * @throws \InvalidArgumentException if $newEmail is not a valid email or insecure
	 * @throws \RangeException if $newEmail is > 128 characters
	 * @throws \TypeError if $newEmail is not a string
	 **/
	public function setProfileEmail(string $newProfileEmail): void {
		//verify the email is secure
		$newProfileEmail = trim($newProfileEmail);
		$newProfileEmail = filter_var($newProfileEmail, FILTER_VALIDATE_EMAIL);
		if(empty($newProfileEmail) === true) {
			throw(new \RangeException("profile email is too large"));
		}
		//store the email
		$this->profileEmail = $newProfileEmail;
	}

	/**
	*accessor method profileHash
	*@return mixed - value for hash
	**/
	 public function getProfileHash(): string {
		return $this->profileHash;
	}

	/**
	 * mutator method for profile hash password
	 * @param string $newProfileHash
	 * @throws \InvalidArgumentException if the hash is not secure
	 * @throws \RangeException if the hash is not 128 characters
	 * @throws \TypeError if profile hash is not a string
	 */
	public function setProfileHash(string $newProfileHash): void {
		//make sure hash is properly formatted
		$newProfileHash = trim($newProfileHash);
		$newProfileHash = strtolower($newProfileHash);
		if(empty($newProfileHash) === true) {
			throw(new \InvalidArgumentException("profile password hash is empty or insecure"));
		}

		//is hash a string representation of a hexadecimal
		if(!ctype_xdigit($newProfileHash)) {
			throw(new \InvalidArgumentException("profile password hash is empty or insecure"));
		}

		//store the hash
		$this->profileHash = $newProfileHash;
	}

		/**
		* accessor method for phone
		* @return string value of phone or null
		 **/
		public function getProfilePhone(): ?string {
			return ($this->profilePhone);
		}

	/**
	 * mutator method for phone
	 * @param string $newProfilePhone new value of phone
	 * @throws \InvalidArgumentException if $newPhone is not a string or insecure
	 * @throws \RangeException if $newPhone is > 32 characters
	 * @throws \TypeError if $newPhone is not a string
	 **/
		public function setProfilePhone(?string $newProfilePhone): void {
			//if $profilephone is null return it right away
			if($newProfilePhone === null) {
				$this->profilePhone = null;
				return;
			}

			//verify phone is secure
			$newProfilePhone = trim($newProfilePhone);
			$newProfilePhone = filter_var($newProfilePhone, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			if(empty($newProfilePhone) === true) {
				throw(new \RangeException("profile phone is too large"));
			}

			//store the phone
			$this->profilePhone = $newProfilePhone;
		}

		/**
		*accessor method for profile salt
		*@return string representation of the salt hexadecimal
		 **/
		public function getProfileSalt(): string {
			return $this->profileSalt;
		}

	/**
	 * mutator method for profile salt
	 * @param string $newProfileSalt
	 * @throws \InvalidArgumentException if the salt is not secure
	 * @throws \RangeException if the salt is not 64 characters
	 * @throws \TypeError if profile salt is not a string
	 */
		public function setProfileSalt(string $newProfileSalt): void {
			//make sure salt is properly formatted
			$newProfileSalt = trim($newProfileSalt);
			$newProfileSalt = strtolower($newProfileSalt );

			//make sure that the salt string representation of a hexadecimal
			if(strlen($newProfileSalt) !== 64) {
				throw(new \RangeException("profile salt must be 128 charcters"));
			}

			//make sure salt is 64 characters
			if(strlen($newProfileSalt) !== 64) {
				throw(new \RangeException("profile salt muct be 128 characters"));
			}

			//store the hash
			$this->profileSalt = $newProfileSalt;
		}


	/**
	 * inserts this Profile into mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
		public function insert(\PDO $pdo): void {
			//make sure the profile is null, do not insert a profile that already exists
			if($this->profileId === null) {
				throw(new \PDOException("unable to delete profile that does not exsist"));
			}

			//create query template
			$query = "UPDATE profile set profileActivationToken = :profileActivationToken, profileAtHandle = :profileAtHandle, profileEmail = :profileEmail, profileHash = :profileHash, profilePhone = :profilePhone, profileSalt = profileSalt WHERE profileId = :profileId";
			$statement = $pdo->prepare($query);

			//bind the member variables to the place holders in the template
			$parameters = ["profileActivationToken" => $this->profileActivationToken, "profileAtHandle" => $this->profileAtHandle, "profileEmail" => $this->profileEmail, "profileHash" => $this->profileHash, "profilePhone" => $this->profilePhone, "profileSalt" => $this->profileSalt];
			$statement->execute($parameters);

			//update the null profileId with what mySQL just gave us
			$this->profileId = intval($pdo->lastInsertId());
		}


	/**
	 * deletes this Profile from mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
		public function delete(\PDO $pdo): void {
			//enforce the profile id is not null, do not delete a profile that does not exist
			if($this->profileId === null) {
				throw(new \PDOException("unable to delete profile that does not exist"));
			}

			//create query template
			$query = "DELETE for profile WHERE profileId = :profileId";
			$statement = $pdo->prepare($query);

			//bind the member variables to the place holders in the template
			//no idea what this means?
			$parameters = ["profileId" => $this->profileId];
			$statement->execute($parameters);
		}


	/**
	 * updates this Profile from mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
		public function update(\PDO $pdo): void {
			//enforce the profile id is not null, do not update profile that does not exist
			if($this->profileId === null) {
				throw(new \PDOException("unable to delete a profile that does not exist"));
			}

			//create query template
			$query = "UPDATE profile SET profileActivationToken = :profileActivationToken, profileAtHandle = :profileAtHandle, profileEmail = :profileEmail, profileHash = :profileHash, profilePhone = :profilePhone, profileSalt = :profileSalt WHERE profileId = :profileId";
			$statement = $pdo->prepare($query);

			//bind the member variables to the place holders in the template
			$parameters = ["profileId" => $this->profileId, "profileActivationToken" => $this->profileActivationToken, "profileAtHandle" => $this->profileAtHandle, "profileEmail" => $this->profileEmail, "profileHash" => $this->profileHash, "profilePhone" => $this->profilePhone, "profileSalt" => $this->profileSalt];
			$statement->execute($parameters);
		}


	/**
	 * gets the Profile by profile id
	 * @param \PDO $pdo $pdo PDO connection object
	 * @param int $profileId profile id to search for
	 * @return Profile|null Profile or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
		public static function getProfileByProfile(\PDO $pdo, int $profileId):?Profile {
			//sanitize the profile Id before searching
			if($profileId <= 0) {
				throw(new \PDOException("profile id is not positive"));
			}

			//create query template
			$query = "SELECT profileId, profileActivationToken, profileAtHandle, profileEmail, profileHash, profilePhone, profileSalt FROM profile WHERE profileId = :profileId";
			$statement = $pdo->prepare($query);

			//bind the profile id to the place holder in the template
			$parameters = ["$profileId" => $profileId];
			$statement->execute($parameters);

			//grab the Profile form SQL
			try {
				$profile = null;
				$statement->setFetchMode(\PDO::FETCH_ASSOC);
				$row = $statement->fetch();
				if($row !== false) {
					$profile = new Profile($row["profileId"], $row["profileActivationToken"], $row["profileAtHandle"], $row["profileEmail"], $row["profileHash"], $row["profilePhone"], $row["profileSalt"]);
				}
			} catch(\Exception $exception) {
				//if the row can not be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
			return ($profile);
		}


	/**
	 * gets the Profile by email
	 * @param \PDO $pdo PDO connection object
	 * @param string $profileEmail email to search for
	 * @return Profile|null Profile or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProfileByProfileEmail(\PDO $pdo, string $profileEmail): ?Profile {
		//sanitize the email before searching
		$profileEmail = trim($profileEmail);
		$profileEmail = filter_var($profileEmail, FILTER_VALIDATE_EMAIL);
		if(empty($profileEmail) === true) {
			throw(new \PDOException("not a valid email"));
		}

		//create query template
		$query = "SELECT profileId, profileActivationToken, profileAtHandle, profileEmail, profileHash, profilePhone, profileSalt FROM profile WHERE profileEmail = :profileEmail";
		$statement = $pdo->prepare($query);

		//bind the profile id to the place holder in the template
		$parameters = ["profileEmail" => $profileEmail];
		$statement->execute($parameters);

		//grab the profile from MySQL
		try {
			$profile = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$profile = new Profile($row["profileId"], $row["profileActivationToken"], $row["profileAtHandle"], $row["profileEmail"], $row["profileHash"], $row["profilePhone"], $row["profileSalt"]);
			}

		}catch(\Exception $exception) {
			//if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return ($profile);
	}


	/**
	 * gets the Profile by at handle
	 * @param \PDO $pdo PDO connection object
	 * @param string $profileAtHandle at handle to search for
	 * @return \SPLFixedArray of all profiles found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProfileByProfileAtHandle(\PDO $pdo, string $profileAtHandle) : \SplFixedArray {
		//sanitize the at handle before searching
		$profileAtHandle = trim($profileAtHandle);
		$profileAtHandle = filter_var($profileAtHandle, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($profileAtHandle) === true) {
			throw(new \PDOException("not a valid at handle"));
		}

		//create query template
		$query = "SELECT  profileId, profileActivationToken, profileAtHandle, profileEmail, profileHash, profilePhone, profileSalt FROM profile WHERE profileAtHandle = :profileAtHandle";
		$statement = $pdo->prepare($query);

		//bind the profile at handle to the place holder in the template
		$parameters = ["profileAtHandle" => $profileAtHandle];
		$statement->execute($parameters);

		$profiles = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);

		while (($row = $statement->fetch()) !== false) {
			try {
				$profile = new Profile($row["profileId"], $row["profileActivationToken"], $row["profileAtHandle"], $row["profileEmail"], $row["profileHash"], $row["profilePhone"], $row["profileSalt"]);
				$profiles[$profiles->key()] = $profile;
				$profiles->next();
			} catch(\Exception $exception) {
				//if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($profiles);
	}


	/**
	 * get the profile by profile activation token
	 * @param string $profileActivationToken
	 * @param \PDO object $pdo
	 * @return Profile|null Profile or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProfileByProfileActivationToken(\PDO $pdo, string $profileActivationToken) : ?Profile {
		//make sure activation token is in thr right format and it is a string representation of a hexadecimal
		$profileActivationToken = trim($profileActivationToken);
		if(ctype_xdigit($profileActivationToken) === false) {
			throw(new \InvalidArgumentException("profile activation token is empty or in the wrong format"));
		}

		//create query template
		$query = "SELECT  profileId, profileActivationToken, profileAtHandle, profileEmail, profileHash, profilePhone, profileSalt FROM profile WHERE profileActivationToken = :profileActivationToken";
		$statement = $pdo->prepare($query);

		//bind the profile activation token to the placeholder in the template
		$parameters = ["$profileActivationToken" => $profileActivationToken];
		$statement->execute($parameters);

		//grab the Profile from mySQL
		try {
			$profile = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$profile = new Profile($row["profileId"], $row["profileActivationToken"], $row["profileAtHandle"], $row["profileEmail"], $row["profileHash"], $row["profilePhone"], $row["profileSalt"]);
			}
		} catch(\Exception $exception) {
			//if the row couldn'd be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return ($profile);
	}

	/**
	*formats the state variables for JSON serialization
	*@return array resulting state variables to serialize
	**/
	public function jsonSerialize() {
		return (get_object_vars($this));
	}
}

