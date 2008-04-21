{if $component.sent == 3 && $component.loggedIn}
    <h1>Eingeloggt</h1>
    <p>Sie wurden erfolgreich eingeloggt.</p>
    Sollte die gewünschte Seite nicht automatisch laden,
    <p><a href="{$component.redirectTo}">klicken Sie bitte hier</a>.</p>
    <script type="text/javascript">
        window.setTimeout("window.location.href = '{$component.redirectTo}'", 2500);
    </script>
{else}
    <h1>Bitte loggen Sie sich ein</h1>
    <p>
        Sie müssen sich einloggen, um die gewünschte Seite zu sehen.
        {if $component.registerUrl}
            <br />Sollten Sie noch keinen Account besitzen, können Sie sich
            <a href="{$component.registerUrl}">hier registrieren</a>.
        {/if}
    </p>

    {if $component.sent != 3}
        {if count($component.errors)}
        <ul class="error">
        {foreach from=$component.errors item=error}
            <li>{$error}</li>
        {/foreach}
        </ul>
        {/if}
        <form action="{$component.action}" method="POST" enctype="{if $component.upload}multipart/form-data{else}application/x-www-form-urlencoded{/if}">
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
            <p>{trlVps text="Please check you values, errors occured"}</p>
        {/if}
    {else}
        {component component=$component.success}
    {/if}
{/if}