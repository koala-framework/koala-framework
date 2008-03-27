{*TODO besser machen, mit echten brotkrümel*}
<div class="vpcPostsWrite">
    <h2>
        <a href="{$component.forumUrl}">{$component.forum}</a> »
        <a href="{$component.groupUrl}">{$component.group|truncate:30:'...':true}</a>
        {if $component.thread}
            » <a href="{$component.threadUrl}" title="{$component.thread}">{$component.thread|truncate:30:'...':true}</a>
        {/if}
    </h2>
    <h3>Nachricht erstellen:</h3>

    {if $component.sent != 3}
        {if $component.sent == 4}
            {*TODO das nicht mit zahlen machen*}
            {component component=$component.preview}
        {/if}
        {if count($component.errors)}
        <ul class="error">
        {foreach from=$component.errors item=error}
            <li>{$error}</li>
        {/foreach}
        </ul>
        {/if}
        <form class="labelTop" action="{$component.action}" method="POST" enctype="{if $component.upload}multipart/form-data{else}application/x-www-form-urlencoded{/if}">
            {foreach from=$component.paragraphs item=paragraph}
                {if $paragraph.store.noCols}
                    {component component=$paragraph}
                {else}
                    <label>{if $paragraph.store.isMandatory} * {/if} {$paragraph.store.fieldLabel}</label>
                    <div class="field">{component component=$paragraph}</div>
                {/if}
            {/foreach}
        </form>
        {if $component.sent == 2}
            <p>{trlVps text="Please check your entry, errors occured"}</p>
        {/if}
    {else}
        {component component=$component.success}
    {/if}

    {if $component.lastPosts}
        <h2 class="lastPosts">{trlVps text="Latest posts from this topic"}</h2>
        {foreach from=$component.lastPosts item=post name=lp}
            {if $smarty.foreach.lp.index < 5}
                {component component=$post}
            {/if}
        {/foreach}
    {/if}

</div>
