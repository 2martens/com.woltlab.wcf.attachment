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
	 * id of the parent object of the object the uploaded attachments belongs to
	 * @var	string
	 */
	_parentObjectID: 0,
	
	/**
	 * container if of WYSIWYG editor
	 * @var	string
	 */
	_wysiwygContainerID: '',
	
	/**
	 * @see	WCF.Upload.init()
	 */
	init: function(buttonSelector, fileListSelector, objectType, objectID, tmpHash, parentObjectID, maxUploads, wysiwygContainerID) {
		this._super(buttonSelector, fileListSelector, 'wcf\\data\\attachment\\AttachmentAction', { multiple: true, maxUploads: maxUploads });
		
		this._objectType = objectType;
		this._objectID = objectID;
		this._tmpHash = tmpHash;
		this._parentObjectID = parentObjectID;
		this._wysiwygContainerID = wysiwygContainerID;
		
		this._buttonSelector.children('p.button').click($.proxy(this._validateLimit, this));
		this._fileListSelector.find('.jsButtonInsertAttachment').click($.proxy(this._insert, this));
	},
	
	/**
	 * Validates upload limits.
	 * 
	 * @return	boolean
	 */
	_validateLimit: function() {
		var $innerError = this._buttonSelector.next('small.innerError');
		
		// check maximum uploads
		var $max = this._options.maxUploads - this._fileListSelector.children('li').length;
		var $filesLength = (this._fileUpload) ? this._fileUpload.prop('files').length : 0;
		if ($max <= 0 || $max < $filesLength) {
			// reached limit
			var $errorMessage = ($max <= 0) ? WCF.Language.get('wcf.attachment.upload.error.reachedLimit') : WCF.Language.get('wcf.attachment.upload.error.reachedRemainingLimit').replace(/#remaining#/, $max);
			if (!$innerError.length) {
				$innerError = $('<small class="innerError" />').insertAfter(this._buttonSelector);
			}
			
			$innerError.html($errorMessage);
			
			// reset value of file input (the 'files' prop is actually readonly!)
			if (this._fileUpload) {
				this._fileUpload.attr('value', '');
			}
			
			return false;
		}
		
		// remove previous errors
		$innerError.remove();
		
		return true;
	},
	
	
	/**
	 * @see	WCF.Upload._upload()
	 */
	_upload: function() {
		if (!this._validateLimit()) {
			return false;
		}
		
		this._super();
		
		// reset value of file input (the 'files' prop is actually readonly!)
		if (this._fileUpload) {
			this._fileUpload.attr('value', '');
		}
	},
	
	/**
	 * @see	WCF.Upload._createUploadMatrix()
	 */
	_createUploadMatrix: function(files) {
		// remove failed uploads
		this._fileListSelector.children('li.uploadFailed').remove();
		
		return this._super(files);
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
		var $li = $('<li class="box48"><span class="icon icon48 icon-spinner" /><div><hgroup><h1>'+file.name+'</h1><h2><progress max="100"></progress></h2></hgroup><ul></ul></div></li>');
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
					$li.children('.icon-spinner').replaceWith($('<img src="' + data.returnValues['attachments'][$filename]['tinyURL'] + '" alt="" class="thumbnail" />'));
				}
				// show file icon
				else {
					$li.children('.icon-spinner').removeClass('icon-spinner').addClass('icon-paper-clip');
				}
				
				// update attachment link
				var $link = $('<a href=""></a>');
				$link.text($filename).attr('href', data.returnValues['attachments'][$filename]['url']);
				
				if (data.returnValues['attachments'][$filename]['isImage'] != 0) {
					console.debug(data.returnValues['attachments'][$filename]['isImage']);
					$link.addClass('jsImageViewer').attr('title', $filename);
				}
				$li.find('h1').empty().append($link);
				
				// update file size
				$li.find('h2').append('<small>'+data.returnValues['attachments'][$filename]['formattedFilesize']+'</small>');
				
				// init buttons
				var $deleteButton = $('<li><span class="icon icon16 icon-remove pointer jsTooltip jsDeleteButton" title="'+WCF.Language.get('wcf.global.button.delete')+'" data-object-id="'+data.returnValues['attachments'][$filename]['attachmentID']+'" data-confirm-message="'+WCF.Language.get('wcf.attachment.delete.sure')+'" /></li>');
				$li.find('ul').append($deleteButton);
				
				var $insertButton = $('<li><span class="icon icon16 icon-paste pointer jsTooltip jsButtonInsertAttachment" title="' + WCF.Language.get('wcf.attachment.insert') + '" data-object-id="' + data.returnValues['attachments'][$filename]['attachmentID'] + '" /></li>');
				$insertButton.children('.jsButtonInsertAttachment').click($.proxy(this._insert, this));
				$li.find('ul').append($insertButton);
			}
			else {
				// upload icon
				$li.children('.icon-spinner').removeClass('icon-spinner').addClass('icon-ban-circle');
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
	},
	
	/**
	 * Inserts an attachment into WYSIWYG editor contents.
	 * 
	 * @param	object		event
	 */
	_insert: function(event) {
		var $attachmentID = $(event.currentTarget).data('objectID');
		var $bbcode = '[attach=' + $attachmentID + '][/attach]';
		
		var $ckEditor = $('#' + this._wysiwygContainerID).ckeditorGet();
		if ($ckEditor.mode === 'wysiwyg') {
			// in design mode
			$ckEditor.insertText($bbcode);
		}
		else {
			// in source mode
			var $textarea = $('#' + this._wysiwygContainerID).next('.cke_editor_text').find('textarea');
			var $value = $textarea.val();
			if ($value.length == 0) {
				$textarea.val($bbcode);
			}
			else {
				var $position = $textarea.getCaret();
				$textarea.val( $value.substr(0, $position) + $bbcode + $value.substr($position) );
			}
		}
	}
});
