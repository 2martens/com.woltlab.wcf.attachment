<?php
namespace wcf\page;
use wcf\data\attachment\Attachment;
use wcf\data\attachment\AttachmentEditor;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows an attachment.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2012 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.attachment
 * @subpackage	page
 * @category 	Community Framework
 */
class AttachmentPage extends AbstractPage {
	/**
	 * @see wcf\page\IPage::$useTemplate
	 */
	public $useTemplate = false;
	
	/**
	 * attachment id
	 * @var integer
	 */
	public $attachmentID = 0;
	
	/**
	 * attachment object
	 * @var wcf\data\attachment\Attachment
	 */
	public $attachment = null;
	
	/**
	 * shows the tiny thumbnail
	 * @var boolean
	 */
	public $tiny = 0;
	
	/**
	 * shows the standard thumbnail
	 * @var boolean
	 */
	public $thumbnail = 0;
	
	
	public static $inlineMimeTypes = array('image/gif', 'image/jpeg', 'image/png', 'application/pdf', 'image/pjpeg');
	
	/**
	 * @see wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->attachmentID = intval($_REQUEST['id']);
		$this->attachment = new Attachment($this->attachmentID);
		if (!$this->attachment->attachmentID) {
			throw new IllegalLinkException();
		}
		if (isset($_REQUEST['tiny']) && $this->attachment->tinyThumbnailType) $this->tiny = intval($_REQUEST['tiny']);
		if (isset($_REQUEST['thumbnail']) && $this->attachment->thumbnailType) $this->thumbnail = intval($_REQUEST['thumbnail']);
		
		if ($this->attachment->tmpHash) {
			if ($this->attachment->userID && $this->attachment->userID != WCF::getUser()->userID) {
				throw new IllegalLinkException();
			}
		}
		
		// check permissions
		if (!$this->attachment->checkPermissions()) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see wcf\page\IPage::show()
	 */
	public function show() {
		parent::show();
		
		// update download count
		if (!$this->tiny && !$this->thumbnail) {
			$editor = new AttachmentEditor($this->attachment);
			$editor->update(array(
				'downloads' => $this->attachment->downloads + 1,
				'lastDownloadTime' => TIME_NOW
			));
		}

		// get file data
		if ($this->tiny) {
			$mimeType = $this->attachment->tinyThumbnailType;
			$filesize = $this->attachment->tinyThumbnailSize;
			$location = $this->attachment->getTinyThumbnailLocation();
		}
		else if ($this->thumbnail) {
			$mimeType = $this->attachment->thumbnailType;
			$filesize = $this->attachment->thumbnailSize;
			$location = $this->attachment->getThumbnailLocation();
		}
		else {
			$mimeType = $this->attachment->fileType;
			$filesize = $this->attachment->filesize;
			$location = $this->attachment->getLocation();
		}		
		
		// send headers
		// file type
		if ($mimeType == 'image/x-png') $mimeType = 'image/png';
		@header('Content-Type: '.$mimeType);
			
		// file name
		@header('Content-disposition: '.(!in_array($mimeType, self::$inlineMimeTypes) ? 'attachment; ' : 'inline; ').'filename="'.$this->attachment->filename.'"');
			
		// send file size
		@header('Content-Length: '.$filesize);
			
		// no cache headers
		if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
			// internet explorer doesn't cache files downloaded from a https website, if 'Pragma: no-cache' was sent 
			// @see http://support.microsoft.com/kb/316431/en
			@header('Pragma: public');
		}
		else {
			@header('Pragma: no-cache');
		}
		@header('Expires: 0');
			
		// show attachment
		readfile($location);
		exit;
	}
}
