{if $component.sent != 3}
    {if count($component.errors)}
    <ul class="error">
    {foreach from=$component.errors item=error}
        <li>{$error}</li>
    {/foreach}
    </ul>
    {/if}
    <form action="{$component.action}" method="POST" enctype="{if $component.upload}multipart/form-data{else}application/x-www-form-urlencoded{/if}">
        <div class="fieldContainer {if !$component.store.isValid}invalid{/if}">
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
    <p>Ihr Formular wurde erfolgreich abgeschickt.</p>
{/if}