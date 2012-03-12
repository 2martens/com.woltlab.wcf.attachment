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
		var $li = $('<li class="wcf-container"><p class="wcf-containerIcon"><img src="'+WCF.Icon.get('wcf.icon.loading')+'" alt="" style="width: 48px; height: 48px" /></p><div class="wcf-containerContent"><p>'+file.name+'</p><p>'+file.size+'</p><p><progress max="100"></progress></p></div></li>');
		this._fileListSelector.append($li);
		
		return $li;
	},
	
	_success: function(uploadID, data) {
		for (var $i = 0; $i < this._uploadMatrix[uploadID].length; $i++) {
			var $li = this._uploadMatrix[uploadID][$i];
			var $filename = $li.data('filename');
			if (data.returnValues[$filename]) {
				// show thumbnail
				if (data.returnValues[$filename]['tinyURL']) {
					$li.find('img').attr('src', data.returnValues[$filename]['tinyURL']);
				}
				else {
					// show file icon
					$li.find('img').attr('src', WCF.Icon.get('wcf.icon.attachment'));
				}
				
				// remove progress bar
				$li.find('progress').remove();
				
				// TODO: add buttons
				
			}
			else {
				// error handling
			}
		}
	}
});