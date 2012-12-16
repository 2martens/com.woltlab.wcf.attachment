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
	/**
	 * object type of the object the uploaded attachments belong to
	 * @var	string
	 */
	_objectType: '',
	
	/**
	 * id of the object the uploaded attachments belong to
	 * @var	string
	 */
	_objectID: 0,
	
	/**
	 * temporary hash to identify uploaded attachments
	 * @var	string
	 */
	_tmpHash: '',
	
	/**
	 * id of the parent object of the object the uploaded attachments belong
	 * to
	 * @var	string
	 */
	_parentObjectID: 0,
	
	/**
	 * @see	WCF.Upload.init()
	 */
	init: function(buttonSelector, fileListSelector, objectType, objectID, tmpHash, parentObjectID, maxUploads) {
		this._super(buttonSelector, fileListSelector, 'wcf\\data\\attachment\\AttachmentAction', { multiple: true, "maxUploads": maxUploads });
		
		this._objectType = objectType;
		this._objectID = objectID;
		this._tmpHash = tmpHash;
		this._parentObjectID = parentObjectID;
	},
	
	/**
	 * @see	WCF.Upload._upload()
	 */
	_upload: function() {
		// remove failed uploads
		this._fileListSelector.find('li.uploadFailed').remove();
		
		this._super();
	},
	
	/**
	 * @see	WCF.Upload._getParameters()
	 */
	_getParameters: function() {
		return {
			objectType: this._objectType,
			objectID: this._objectID,
			tmpHash: this._tmpHash,
			parentObjectID: this._parentObjectID
		};
	},
	
	/**
	 * @see	WCF.Upload._initFile()
	 */
	_initFile: function(file) {
		var $li = $('<li class="box48"><img src="'+WCF.Icon.get('wcf.icon.loading')+'" alt="" style="width: 48px; height: 48px" /><div><hgroup><h1>'+file.name+'</h1><h2><progress max="100"></progress></h2></hgroup><ul></ul></div></li>');
		this._fileListSelector.append($li);
		this._fileListSelector.show();
		
		return $li;
	},
	
	/**
	 * @see	WCF.Upload._success()
	 */
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
				
				// update attachment link
				var $link = $('<a href=""></a>');
				$link.text($filename).attr('href', data.returnValues['attachments'][$filename]['url']);
				
				if (data.returnValues['attachments'][$filename]['isImage'] != 0) {
					console.debug(data.returnValues['attachments'][$filename]['isImage']);
					$link.attr('rel', 'imageviewer').attr('title', $filename);
				}
				$li.find('h1').empty().append($link);
				
				// update file size
				$li.find('h2').append('<small>'+data.returnValues['attachments'][$filename]['formattedFilesize']+'</small>');
				
				// init buttons
				var $deleteButton = $('<li><img src="'+WCF.Icon.get('wcf.icon.delete')+'" alt="" title="'+WCF.Language.get('wcf.global.button.delete')+'" class="jsDeleteButton jsTooltip pointer" data-object-id="'+data.returnValues['attachments'][$filename]['attachmentID']+'" data-confirm-message="'+WCF.Language.get('wcf.attachment.delete.sure')+'" /></li>');
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
					$errorMessage = 'uploadFailed';
				}
				
				$li.find('hgroup').append($('<small class="innerError">'+WCF.Language.get('wcf.attachment.upload.error.'+$errorMessage)+'</small>'));
				$li.addClass('uploadFailed');
			}
			
			// fix webkit rendering bug
			$li.css('display', 'block');
		}
		
		WCF.DOMNodeInsertedHandler.forceExecution();
	}
});