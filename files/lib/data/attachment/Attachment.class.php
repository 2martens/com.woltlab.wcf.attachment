<?php
namespace wcf\data\attachment;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;

/**
 * Represents an attachment.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.attachment
 * @subpackage	data.attachment
 * @category 	Community Framework
 */
class Attachment extends DatabaseObject implements IRouteController {
	/**
	 * @see	wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'attachment';
	
	/**
	 * @see	wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'attachmentID';
	
	/**
	 * Returns true, if a user has the permission to download this attachment.
	 * 
	 * @return boolean
	 */
	public function checkPermissions() {
		$objectType = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID);
		$processor = $objectType->getProcessor();
		if ($processor !== null) {
			return $processor->checkPermissions($this->objectID);
		}
		
		return true;
	}
	
	/**
	 * Returns the physical location of this attachment.
	 * 
	 * @return string
	 */
	public function getLocation() {
		return self::getStorage() . substr($this->fileHash, 0, 2) . '/' . ($this->attachmentID) . '-' . $this->fileHash;
	}
	
	/**
	 * Returns the physical location of the tiny thumbnail.
	 * 
	 * @return string
	 */
	public function getTinyThumbnailLocation() {
		return self::getStorage() . substr($this->fileHash, 0, 2) . '/' . ($this->attachmentID) . '-tiny-' . $this->fileHash;
	}
	
	/**
	 * Returns the physical location of the standard thumbnail.
	 * 
	 * @return string
	 */
	public function getThumbnailLocation() {
		return self::getStorage() . substr($this->fileHash, 0, 2) . '/' . ($this->attachmentID) . '-thumbnail-' . $this->fileHash;
	}
	
	/**
	 * @see	wcf\system\request\IRouteController::getID()
	 */
	public function getID() {
		return $this->attachmentID;
	}
	
	/**
	 * @see	wcf\system\request\IRouteController::getTitle()
	 */
	public function getTitle() {
		return $this->filename;
	}
	
	/**
	 * Returns the storage path.
	 * 
	 * @return string
	 */
	public static function getStorage() {
		if (ATTACHMENT_STORAGE) {
			return ATTACHMENT_STORAGE;
		}
		
		return WCF_DIR . 'attachments/';
	}
}
