{*TODO besser machen, mit echten brotkrümel*}
<div class="vpcPostsWrite">
<h1>
    <a href="{$component.forumUrl}">{$component.forum}</a> »
    <a href="{$component.groupUrl}">{$component.group}</a>
    {if $component.thread}
        » <a href="{$component.threadUrl}">{$component.thread}</a>
    {/if}
</h1>
<h2>Nachricht erstellen:</h2>

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
        <p>Bitte überprüfen Sie Ihre Eingabe, es traten Fehler auf.</p>
    {/if}
{else}
    {component component=$component.success}
{/if}
</div>