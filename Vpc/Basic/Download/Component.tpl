{strip}
{if $component.downloadTag.url}
{component component=$component.downloadTag}
    {$component.infotext}
    {if $component.downloadTag.filesize > 0} ({$component.downloadTag.filesize|file_size}) {/if}
</a>
{/if}
{/strip}