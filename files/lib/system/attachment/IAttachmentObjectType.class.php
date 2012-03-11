<?php
namespace wcf\system\attachment;

interface IAttachmentObjectType {
	/**
	 * Returns true, if a user has the permission to download attachments.
	 * 
	 * @param	integer		$objectID
	 * @return	boolean
	 */
	public function canDownload($objectID);
	
	/**
	 * Returns true, if a user has the permission to view attachment previews (thumbnails).
	 * 
	 * @param	integer		$objectID
	 * @return	boolean
	 */
	public function canViewPreview($objectID);
	
	/**
	 * Returns true, if a user has the permission to upload attachments.
	 * 
	 * @param	integer		$objectID
	 * @param	integer		$parentObjectID
	 * @return	boolean
	 */
	public function canUpload($objectID, $parentObjectID = 0);
	
	/**
	 * Returns the maximum filesize for an attachment.
	 * 
	 * @return	integer
	 */
	public function getMaxSize();
	
	/**
	 * Returns the allowed file extensions.
	 * 
	 * @return	string
	 */
	public function getAllowedExtensions();
	
	/**
	 * Returns the maximum number of attachments.
	 * 
	 * @return	integer
	 */
	public function getMaxCount();
}
