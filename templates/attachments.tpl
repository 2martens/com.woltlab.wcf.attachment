{if $attachmentList && $attachmentList->getGroupedObjects($objectID)|count}
	{hascontent}
		<fieldset>
			<legend>{lang}wcf.attachment.images{/lang}</legend>
			
			<ul>
				{content}
					{foreach from=$attachmentList->getGroupedObjects($objectID) item=attachment}
						{if $attachment->isImage}
							<li style="float: left; margin-right: 7px; margin-bottom: 7px; width: 144px; height: 144px;" class="jsTooltip" title="{lang}wcf.attachment.image.info{/lang}">
								{if $attachment->tinyThumbnailType}
									<a href="{link controller='Attachment' object=$attachment}{/link}"><img src="{link controller='Attachment' object=$attachment}tiny=1{/link}" alt="" style="width: 144px; height: 144px; border-radius: 10px; box-shadow: 2px 2px 7px rgba(0, 0, 0, .5);" /></a>
								{else}
									<img src="{link controller='Attachment' object=$attachment}{/link}" alt="" style="width: {@$attachment->width}px; height: {@$attachment->height}px; margin-top: {@(144-$attachment->height)/2}px; margin-left: {@(144-$attachment->width)/2}px; border-radius: 10px; box-shadow: 2px 2px 7px rgba(0, 0, 0, .5);" />
								{/if}
							</li>
						{/if}
					{/foreach}
				{/content}	
			</ul>
		</fieldset>
	{/hascontent}
	
	{hascontent}
		<fieldset>
			<legend>{lang}wcf.attachment.files{/lang}</legend>
			
			<ul>
				{content}
					{foreach from=$attachmentList->getGroupedObjects($objectID) item=attachment}
						{if !$attachment->isImage}
							<li class="wcf-container">
								<a href="{link controller='Attachment' object=$attachment}{/link}" class="wcf-containerIcon"><img src="{icon size='L'}attachment1{/icon}" alt="" /></a>
								
								<div class="wcf-containerContent">
									<h1><a href="{link controller='Attachment' object=$attachment}{/link}">{$attachment->filename}</a></h1>
									<p>{lang}wcf.attachment.file.info{/lang}</p>	
								</div>
							</li>
						{/if}
					{/foreach}
				{/content}	
			</ul>
		</fieldset>
	{/hascontent}
{/if}