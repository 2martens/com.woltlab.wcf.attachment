<?php
namespace wcf\data\attachment;
use wcf\data\DatabaseObjectEditor;

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
}
