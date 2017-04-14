DROP TABLE IF EXISTS favorite;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS profile;


CREATE TABLE profile (

profileID INT UNSIGNED AUTO_INCREMENT NOT NULL,

--NOT SURE IF WE NEED THIS
--profileActivationToken char(32)

--what does this have to do with favoriting a product?
profileHandle VARCHAR (32)

profileEmail VARCHAR (128) UNIQUE NOT NULL,

profileHash CHAR(128) NOT NULL,

--I do not think we need this?
profilePhone VARCHAR (32),

profileSalt CHAR (64 NOT NULL,

UNIQUE(profileEmail),

--again what does this have to do with favoriting a product?
UNIQUE (profileHandle),

PRIMARY KEY (profileId),

);


