/**
 * Namespace for attachments
 */
WCF.Attachment = {};

/**
 * Attachment upload function
 * 
 * @see	WCF.Upload
 */
WCF.Attachment.Upload = WCF.Upload.extend({
	_objectType: '',
	_objectID: 0,
	_tmpHash: '',
	_parentObjectID: 0,
	
	init: function(buttonSelector, fileListSelector, objectType, objectID, tmpHash, parentObjectID) {
		this._super(buttonSelector, fileListSelector, 'wcf\\data\\attachment\\AttachmentAction', { multiple: true });
		
		this._objectType = objectType;
		this._objectID = objectID;
		this._tmpHash = tmpHash;
		this._parentObjectID = parentObjectID;
	},

	_getParameters: function() {
		return {
			objectType: this._objectType,
			objectID: this._objectID,
			tmpHash: this._tmpHash,
			parentObjectID: this._parentObjectID
		};
	},
	
	_initFile: function(file) {
		var $li = $('<li class="box48"><img src="'+WCF.Icon.get('wcf.icon.loading')+'" alt="" style="width: 48px; height: 48px" /><div><hgroup><h1>'+file.name+'</h1><h2><small>'+file.size+'</small></h2><h3><progress max="100"></progress></h3></hgroup><ul></ul></div></li>');
		this._fileListSelector.append($li);
		this._fileListSelector.show();
		
		return $li;
	},
	
	_success: function(uploadID, data) {
		for (var $i = 0; $i < this._uploadMatrix[uploadID].length; $i++) {
			// get li
			var $li = this._uploadMatrix[uploadID][$i];
			
			// remove progress bar
			$li.find('progress').remove();
			
			// get filename and check result
			var $filename = $li.data('filename');
			if (data.returnValues && data.returnValues['attachments'][$filename]) {
				// show thumbnail
				if (data.returnValues['attachments'][$filename]['tinyURL']) {
					$li.find('img').attr('src', data.returnValues['attachments'][$filename]['tinyURL']).addClass('thumbnail');
				}
				// show file icon
				else {
					$li.find('img').attr('src', WCF.Icon.get('wcf.icon.attachment'));
				}
				
				// init buttons
				var $deleteButton = $('<li><img src="'+WCF.Icon.get('wcf.icon.delete')+'" alt="" title="'+WCF.Language.get('wcf.global.button.delete')+'" class="jsDeleteButton jsTooltip" data-object-id="'+data.returnValues['attachments'][$filename]['attachmentID']+'" data-confirm-message="'+WCF.Language.get('wcf.attachment.delete.sure')+'" /></li>');
				$li.find('ul').append($deleteButton);
			}
			else {
				// upload icon
				$li.find('img').attr('src', WCF.Icon.get('wcf.icon.error'));
				var $errorMessage = '';
				
				// error handling
				if (data.returnValues && data.returnValues['errors'][$filename]) {					
					$errorMessage = data.returnValues['errors'][$filename]['errorType'];
				}
				else {
					// unknown error
					$errorMessage = 'unknown error';
				}
				
				$li.find('hgroup').append($('<small class="innerError">'+$errorMessage+'</small>'));
			}
		}
	}
});