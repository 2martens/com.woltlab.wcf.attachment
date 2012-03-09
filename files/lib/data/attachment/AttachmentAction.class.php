<?php
namespace wcf\data\attachment;
use wcf\system\request\LinkHandler;

use wcf\data\object\type\ObjectTypeCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\ValidateActionException;
use wcf\system\image\ImageHandler;
use wcf\system\WCF;

// TODO: debug; remove later
if (!defined('ATTACHMENT_THUMBNAIL_WIDTH')) define('ATTACHMENT_THUMBNAIL_WIDTH', 320);
if (!defined('ATTACHMENT_THUMBNAIL_HEIGHT')) define('ATTACHMENT_THUMBNAIL_HEIGHT', 320);

/**
 * Executes attachment-related actions.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2012 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.attachment
 * @subpackage	data.attachment
 * @category 	Community Framework
 */
class AttachmentAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\attachment\AttachmentEditor';
	
	/**
	 * Validates the upload action.
	 */
	public function validateUpload() {
		// validate object type
		if (!isset($this->parameters['objectType'])) {
			throw new ValidateActionException("missing parameter 'objectType'");
		}
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.attachment.objectType', $this->parameters['objectType']);
		if ($objectType === null) {
			throw new ValidateActionException("invalid object type '".$this->parameters['objectType']."' given");
		}
		
		// TODO: validate object id / tmpHash
		
		// TODO: check upload permissions
		
		// TODO: check max filesize, allowed file extensions etc.
	}
	
	/**
	 * Handles uploaded attachments.
	 */
	public function upload() {
		// get object type
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.attachment.objectType', $this->parameters['objectType']);
		
		// save files
		$thumbnails = $attachments = array();
		$files = $this->parameters['__files']->getFiles();
		foreach ($files as $file) {
			$data = array(
				'objectTypeID' => $objectType->objectTypeID,
				'objectID' => intval($this->parameters['objectID']),
				'userID' => WCF::getUser()->userID,
				'tmpHash' => $this->parameters['tmpHash'],
				'filename' => $file->getFilename(),
				'filesize' => $file->getFilesize(),
				'fileType' => $file->getMimeType(),
				'fileHash' => sha1_file($file->getLocation()),
				'uploadTime' => TIME_NOW	
			);
			
			// get image data
			if (($imageData = $file->getImageData()) !== null) {
				$data['isImage'] = 1;
				$data['width'] = $imageData['width'];
				$data['height'] = $imageData['height'];
				$data['fileType'] = $imageData['mimeType'];
			}
			
			// create attachment
			$attachment = AttachmentEditor::create($data);
			
			// check attachment directory
			// and create subdirectory if necessary
			$dir = dirname($attachment->getLocation());
			if (!@file_exists($dir)) {
				@mkdir($dir, 0777);
			}
			
			// move uploaded file
			if (@move_uploaded_file($file->getLocation(), $attachment->getLocation())) {
				if ($attachment->isImage) {
					$thumbnails[] = $attachment;
				}
				$attachments[] = $attachment;
			}
			else {
				// moving failed; delete attachment
				$editor = new AttachmentEditor($attachment);
				$editor->delete();
			}
		}
		
		// generate thumbnails
		if (count($thumbnails)) {
			$action = new AttachmentAction($thumbnails, 'generateThumbnails');
			$action->executeAction();
		}
		
		// return result
		$result = array();
		foreach ($attachments as $attachment) {
			$result[$attachment->filename] = array(
				'filename' => $attachment->filename,
				'filesize' => $attachment->filesize,
				'isImage' => $attachment->isImage,
				'attachmentID' => $attachment->attachmentID,
				'tinyURL' => LinkHandler::getInstance()->getLink('Attachment', array('object' => $attachment), 'tiny=1'),
				'thumbnailURL' => LinkHandler::getInstance()->getLink('Attachment', array('object' => $attachment), 'thumbnail=1'),
				'url' => LinkHandler::getInstance()->getLink('Attachment', array('object' => $attachment))
			);
		}
		return $result;
	}
	
	/**
	 * Generates thumbnails.
	 */
	public function generateThumbnails() {
		if (!count($this->objects)) {
			$this->readObjects();
		}
		
		foreach ($this->objects as $attachment) {
			$adapter = ImageHandler::getInstance()->getAdapter();
			$adapter->loadFile($attachment->getLocation());
			$updateData = array();
			
			// create tiny thumbnail
			$tinyThumbnailLocation = $attachment->getTinyThumbnailLocation();
			$thumbnail = $adapter->createThumbnail(144, 144, false);
			$adapter->writeImage($thumbnail, $tinyThumbnailLocation);
			if (file_exists($tinyThumbnailLocation) && ($imageData = @getImageSize($tinyThumbnailLocation)) !== false) {
				$updateData['tinyThumbnailType'] = $imageData['mime'];
				$updateData['tinyThumbnailSize'] = @filesize($tinyThumbnailLocation);
				$updateData['tinyThumbnailWidth'] = $imageData[0];
				$updateData['tinyThumbnailHeight'] = $imageData[1];
			}
			
			// create standard thumbnail
			$thumbnailLocation = $attachment->getThumbnailLocation();
			$thumbnail = $adapter->createThumbnail(ATTACHMENT_THUMBNAIL_WIDTH, ATTACHMENT_THUMBNAIL_HEIGHT);
			$adapter->writeImage($thumbnail, $thumbnailLocation);
			if (file_exists($thumbnailLocation) && ($imageData = @getImageSize($thumbnailLocation)) !== false) {
				$updateData['thumbnailType'] = $imageData['mime'];
				$updateData['thumbnailSize'] = @filesize($thumbnailLocation);
				$updateData['thumbnailWidth'] = $imageData[0];
				$updateData['thumbnailHeight'] = $imageData[1];
			}
			
			if (count($updateData)) {
				$attachment->update($updateData);
			}
		}
	}
}
