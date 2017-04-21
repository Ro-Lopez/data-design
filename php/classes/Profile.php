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

	/**
	 * @param mixed $profileActivationToken
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
		$this->ProfileAtHandle = $newProfileAtHandle;

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
	public function setPrifle


}
