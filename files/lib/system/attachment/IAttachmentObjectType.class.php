<?php
namespace wcf\system\attachment;

interface IAttachmentObjectType {
	/**
	 * Returns true, if a user has the permission to download an attachment.
	 * 
	 * @param	integer		$objectID
	 * @return	boolean
	 */
	public function checkPermissions($objectID);
}
