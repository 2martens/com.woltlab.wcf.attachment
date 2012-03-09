{if $attachmentList && $attachmentList->getGroupedObjects($objectID)|count}
	<fieldset>
		<legend>attachments</legend>
		
		<ul>
			{foreach from=$attachmentList->getGroupedObjects($objectID) item=attachment}
				{if $attachment->isImage}
					<li style="float: left; margin-right: 7px" class="jsTooltip" title="{$attachment->filename}, {@$attachment->filesize|filesize}, {#$attachment->width}x{#$attachment->height}"><a href="{link controller='Attachment' object=$attachment}{/link}"><img src="{link controller='Attachment' object=$attachment}tiny=1{/link}" alt="" style="border-radius: 10px; box-shadow: 2px 2px 7px rgba(0, 0, 0, .5);" /></a></li>
				{else}
					<li><a href="{link controller='Attachment' object=$attachment}{/link}">{$attachment->filename}</a></li>
				{/if}
			{/foreach}
		</ul>
	</fieldset>
{/if}