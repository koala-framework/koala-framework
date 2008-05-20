{component component=$component.paging}
<ul>
{foreach from=$component.users item=u}
    <li>
        <a href="{$u.url}">{$u.name}</a>
        {if $u.created}
        <span> ( {trlVps text="Member since"}: {$u.created|date_format:"%d.%m.%y"} )</span>
        {/if}
    </li>
{/foreach}
</ul>
{component component=$component.paging}