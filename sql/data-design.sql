DROP TABLE IF EXISTS favorite;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS profile;

CREATE TABLE profile (
	profileId INT UNSIGNED AUTO_INCREMENT NOT NULL,
	profileActivationToken char(32),
	profileAtHandle VARCHAR(32)NOT NULL,
	profileEmail VARCHAR(128) UNIQUE NOT NULL,
	profileHash CHAR(128) NOT NULL,
	profilePhone VARCHAR(32),
	profileSalt CHAR(64) NOT NULL,
	UNIQUE(profileEmail),
	UNIQUE(profileAtHandle),
	PRIMARY KEY(profileId)
);

CREATE TABLE product (
	productId INT UNSIGNED AUTO_INCREMENT NOT NULL,
	productProfileId INT UNSIGNED NOT NULL,
	productContent VARCHAR(140) NOT NULL,
	productDate DATETIME NOT NULL,
	INDEX(productProfileId),
	FOREIGN KEY(productProfileId) REFERENCES profile(profileId),
	PRIMARY KEY(productId)
);

CREATE TABLE favorite (
	favoriteProfileId INT UNSIGNED NOT NULL,
	favoriteProductId INT UNSIGNED NOT NULL,
	favoriteDate DATETIME NOT NULL,
	INDEX(favoriteProfileId),
	INDEX(favoriteProductId),
	FOREIGN KEY(favoriteProfileId) REFERENCES profile(profileId),
	FOREIGN KEY(favoriteProductId) REFERENCES product(productId),
	PRIMARY KEY(favoriteProfileId, favoriteProductId)
);











