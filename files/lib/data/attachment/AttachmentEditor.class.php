<?php
namespace wcf\data\attachment;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit attachments.
 *
 * @author	Marcel Werk
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.attachment
 * @subpackage	data.attachment
 * @category 	Community Framework
 */
class AttachmentEditor extends DatabaseObjectEditor {
	/**
	 * @see	wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	public static $baseClass = 'wcf\data\attachment\Attachment';
	
	/**
	 * @see wcf\data\IEditableObject::delete()
	 */
	public function delete() {
		$sql = "DELETE FROM	wcf".WCF_N."_attachment
			WHERE		attachmentID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->attachmentID));
		
		$this->deleteFiles();
	}
	
	/**
	 * @see wcf\data\IEditableObject::deleteAll()
	 */
	public static function deleteAll(array $objectIDs = array()) {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_attachment
			WHERE	attachmentID IN (".str_repeat('?,', count($objectIDs) - 1)."?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($objectIDs);
		while ($attachment = $statement->fetchObject(self::$baseClass)) {
			$editor = new AttachmentEditor($attachment);
			$editor->deleteFiles();
		}
		
		return parent::deleteAll($objectIDs);
	}
	
	public function deleteFiles() {
		@unlink($this->getLocation());
		if ($this->tinyThumbnailType) {
			@unlink($this->getTinyThumbnailLocation());
		}
		if ($this->thumbnailType) {
			@unlink($this->getThumbnailLocation());
		}
	}
}
