<?php

namespace Edu\Cnm\DataDesign;

//	cross section of a product profile

//this is the section for the product profile

//@author rolopez <llopez165@cnm.edu> - pretty much copied from @deepdivedylan (non ctrl c and ctrl v practices bro)


class Profile {

	//id for this Profile; primary key
	//@var int $profileId
	private $profileId;

	//at handle for this Profile; this is a unique index
	//@var string $profileAtHandle
	private $profileAtHandle;

	//token (usually an email?) handed out to verify that profile is valid
	//@var $profileActivationToken;
	private $profileActivationToken;

	//email for this Profile; this is a unique index
	//@var string $profileEmail
	private $profileEmail;

	//hash for profile pw
	//@var $profileHash
	private $profileHash;

	//phone number for this Profile
	//@var string $profilePhone
	private $profilePhone;

	//salt for this profile password
	//@var $profileSalt
	private $profileSalt;


	//constructor for this profile

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


	//accessor method for profile id
	//@return int value of profile id (or null if new profile)
	public function getProfileId() {
		return $this->profileId;
	}

	//mutator method for profile Id
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


	//accessor method for account activation token
	 // @return mixed - string value of the activation token
	public function getProfileActivationToken() {
		return ($this->profileActivationToken);
	}

	//mutator for account activation token
	//@param mixed $profileActivationToken
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


	//accessor method for at handle
	//@return mixed - string value for handle
	public function getProfileAtHandle(): string {
		return $this->profileAtHandle;
	}

	//mutator method for handle
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


	//accessor method for email
	//@return mixed string value of email
	public function getProfileEmail(): string {
		return $this->profileEmail;
	}

	//mutator method for email
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


	//accessor method profileHash
	//@return mixed - value for hash
	public function getProfileHash(): string {
		return $this->profileHash;
	}

	//mutator method for profile hash password
	//@param mixed $profileHash
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


		//accessor method for phone
		public function getProfilePhone(): ?string {
			return ($this->profilePhone);
		}

		//mutator method for phone
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


		//accessor method for profile salt
		public function getProfileSalt(): string {
			return $this->profileSalt;
		}

		//mutator method for profile salt
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


		public function insert(\PDO $pdo): void {
			//make sure the profile is null, dont insert a profile that already exsists
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



}

