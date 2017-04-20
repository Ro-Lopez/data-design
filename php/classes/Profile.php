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

}
