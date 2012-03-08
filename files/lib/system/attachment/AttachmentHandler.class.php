<?php
namespace wcf\system\attachment;
use wcf\data\attachment\AttachmentList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

class AttachmentHandler implements \Countable {
	/**
	 * object type
	 * @var wcf\data\object\type\ObjectType
	 */
	protected $objectType = null;
	
	/**
	 * object id
	 * @var integer
	 */
	protected $objectID = 0;
	
	/**
	 * temp hash
	 * @var string
	 */
	protected $tmpHash = '';
	
	/**
	 * list of attachments
	 * @var wcf\data\attachment\AttachmentList
	 */
	protected $attachmentList = null;
	
	/**
	 * Creates a new AttachmentHandler object.
	 * 
	 * @param	string		$objectType
	 * @param	integer		$objectID
	 * @param	string		$tmpHash
	 */
	public function __construct($objectType, $objectID, $tmpHash = '') {
		$this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.attachment.objectType', $objectType);
		$this->objectID = $objectID;
		$this->tmpHash = $tmpHash;
	}
	
	/**
	 * Returns a list of attachments.
	 * 
	 * @return wcf\data\attachment\AttachmentList
	 */
	public function getAttachmentList() {
		if ($this->attachmentList === null) {
			$this->attachmentList = new AttachmentList();
			$this->attachmentList->sqlLimit = 0;
			$this->attachmentList->sqlOrderBy = 'attachment.showOrder';
			$this->attachmentList->getConditionBuilder()->add('objectTypeID = ?', array($this->objectType->objectTypeID));
			if ($this->objectID) {
				$this->attachmentList->getConditionBuilder()->add('objectID = ?', array($this->objectID));
			}
			else {
				$this->attachmentList->getConditionBuilder()->add('tmpHash = ?', array($this->tmpHash));
			}
			$this->attachmentList->readObjects();
		}
		
		return $this->attachmentList;
	}
	
	/**
	 * @see \Countable::count()
	 */
	public function count() {
		return count($this->getAttachmentList());
	}
	
	/**
	 * Sets the object id of temporary saved attachments.
	 * 
	 * @param	integer		$objectID
	 */
	public function updateObjectID($objectID) {
		$sql = "UPDATE	wcf".WCF_N."_attachment
			SET	objectID = ?,
				tmpHash = ''
			WHERE	objectTypeID = ?
				AND tmpHash = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($objectID, $this->objectType->objectTypeID, $this->tmpHash));
	}
}
