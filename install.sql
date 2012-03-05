DROP TABLE IF EXISTS wcf1_attachment;
CREATE TABLE wcf1_attachment (
	attachmentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	packageID INT(10) NOT NULL,
	objectTypeID INT(10) NOT NULL,
	objectID INT(10),
	userID INT(10),
	idHash VARCHAR(40) NOT NULL DEFAULT '',
	attachmentName VARCHAR(255) NOT NULL DEFAULT '',
	attachmentSize INT(10) NOT NULL DEFAULT 0,
	fileType VARCHAR(255) NOT NULL DEFAULT '',
	isImage TINYINT(1) NOT NULL DEFAULT 0,
	width SMALLINT(5) NOT NULL DEFAULT 0,
	height SMALLINT(5) NOT NULL DEFAULT 0, 
	thumbnailType VARCHAR(255) NOT NULL DEFAULT '',
	thumbnailSize INT(10) NOT NULL DEFAULT 0,
	thumbnailWidth SMALLINT(5) NOT NULL DEFAULT 0,
	thumbnailHeight SMALLINT(5) NOT NULL DEFAULT 0,
	downloads INT(10) NOT NULL DEFAULT 0,
	lastDownloadTime INT(10) NOT NULL DEFAULT 0,
	uploadTime INT(10) NOT NULL DEFAULT 0,
	embedded TINYINT(1) NOT NULL DEFAULT 0,
	showOrder SMALLINT(5) NOT NULL DEFAULT 0,
	KEY (packageID, objectTypeID, objectID),
	KEY (packageID, objectTypeID, idHash),
	KEY (userID, packageID)
);

ALTER TABLE wcf1_attachment ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
ALTER TABLE wcf1_attachment ADD FOREIGN KEY (objectTypeID) REFERENCES wcf1_object_type (objectTypeID) ON DELETE CASCADE;
ALTER TABLE wcf1_attachment ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;