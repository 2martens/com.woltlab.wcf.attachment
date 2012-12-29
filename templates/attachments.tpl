{if $attachmentList && $attachmentList->getGroupedObjects($objectID)|count}
	{hascontent}
		<div class="attachmentThumbnailList">
			<fieldset>
				<legend>{lang}wcf.attachment.images{/lang}</legend>
				
				<ul>
					{content}
						{foreach from=$attachmentList->getGroupedObjects($objectID) item=attachment}
							{if $attachment->isImage && !$attachment->isEmbedded()}
								<li class="attachmentThumbnail">
									{if $attachment->thumbnailType}
										<a href="{link controller='Attachment' object=$attachment}{/link}" class="jsImageViewer" title="{$attachment->filename}"><img src="{link controller='Attachment' object=$attachment}thumbnail=1{/link}" alt="" /></a>
									{else}
										<img src="{link controller='Attachment' object=$attachment}{/link}" alt="" />
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
							{if !$attachment->isImage && !$attachment->isEmbedded()}
								<li class="box24">
									<a href="{link controller='Attachment' object=$attachment}{/link}"><img src="{icon}attachment{/icon}" alt="" class="icon24" /></a>
									
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