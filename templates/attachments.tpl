{if $attachmentList && $attachmentList->getGroupedObjects($objectID)|count}
	{hascontent}
		<div class="attachmentThumbnailList">
			<fieldset>
				<legend>{lang}wcf.attachment.images{/lang}</legend>
				
				<ul>
					{content}
						{foreach from=$attachmentList->getGroupedObjects($objectID) item=attachment}
							{if $attachment->showAsImage() && !$attachment->isEmbedded()}
								<li class="attachmentThumbnail">
									{if $attachment->hasThumbnail()}
										<a href="{link controller='Attachment' object=$attachment}{/link}"{if $attachment->canDownload()} class="jsImageViewer" title="{$attachment->filename}"{/if}><img src="{link controller='Attachment' object=$attachment}thumbnail=1{/link}" alt="" /></a>
									{else}
										<img src="{link controller='Attachment' object=$attachment}{/link}" alt="" style="margin-top: {@ATTACHMENT_THUMBNAIL_HEIGHT/2-$attachment->height/2}px; margin-left: {@ATTACHMENT_THUMBNAIL_WIDTH/2-$attachment->width/2}px" />
									{/if}
									
									<hgroup title="{lang}wcf.attachment.image.info{/lang}">
										<h1>{$attachment->filename}</h1>
										<h2>{lang}wcf.attachment.image.info{/lang}</h2>
									</hgroup>
								</li>
							{/if}
						{/foreach}
					{/content}
				</ul>
			</fieldset>
		</div>
	{/hascontent}
		
	{hascontent}
		<div class="attachmentFileList">
			<fieldset>
				<legend>{lang}wcf.attachment.files{/lang}</legend>
				
				<ul>
					{content}
						{foreach from=$attachmentList->getGroupedObjects($objectID) item=attachment}
							{if $attachment->showAsFile() && !$attachment->isEmbedded()}
								<li class="box32">
									<a href="{link controller='Attachment' object=$attachment}{/link}"><span class="icon icon32 icon-paper-clip"></span></a>
									
									<hgroup>
										<h1><a href="{link controller='Attachment' object=$attachment}{/link}">{$attachment->filename}</a></h1>
										<h2>{lang}wcf.attachment.file.info{/lang}</h2>
									</hgroup>
								</li>
							{/if}
						{/foreach}
					{/content}
				</ul>
			</fieldset>
		</div>
	{/hascontent}
{/if}