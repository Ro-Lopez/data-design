
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



}
