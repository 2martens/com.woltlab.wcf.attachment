{if $attachmentList && $attachmentList->getGroupedObjects($objectID)|count}
	{hascontent}
		<fieldset class="attachmentThumbnailList">
			<legend>{lang}wcf.attachment.images{/lang}</legend>
			
			<ul>
				{content}
					{foreach from=$attachmentList->getGroupedObjects($objectID) item=attachment}
						{if $attachment->isImage}
							<li class="attachmentThumbnail">
								{if $attachment->thumbnailType}
									<a href="{link controller='Attachment' object=$attachment}{/link}" rel="imageviewer" title="{$attachment->filename}"><img src="{link controller='Attachment' object=$attachment}thumbnail=1{/link}" alt="" style="{*width: 144px; height: 144px; border-radius: 10px; box-shadow: 2px 2px 7px rgba(0, 0, 0, .5);*}" /></a>
								{else}
									<img src="{link controller='Attachment' object=$attachment}{/link}" alt="" style="{*width: {@$attachment->width}px; height: {@$attachment->height}px; margin-top: {@(144-$attachment->height)/2}px; margin-left: {@(144-$attachment->width)/2}px; border-radius: 10px; box-shadow: 2px 2px 7px rgba(0, 0, 0, .5);*}" />
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
	{/hascontent}
	
	{hascontent}
		<fieldset class="attachmentFileList">
			<legend>{lang}wcf.attachment.files{/lang}</legend>
			
			<ul>
				{content}
					{foreach from=$attachmentList->getGroupedObjects($objectID) item=attachment}
						{if !$attachment->isImage}
							<li class="box24">
								<a href="{link controller='Attachment' object=$attachment}{/link}"><img src="{icon size='L'}attachment{/icon}" alt="" class="icon24" /></a>
								
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
	{/hascontent}
{/if}