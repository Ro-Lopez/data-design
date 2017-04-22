<?php

namespace Edu\Cnm\DataDesign;

require_once("autoload.php");

//favoriteProfileId, favoriteProductId, favoriteDate

class Favorite {

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








}

