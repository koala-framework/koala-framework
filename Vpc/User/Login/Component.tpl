{if $component.sent == 3 && $component.loggedIn}
    <h1>Eingeloggt</h1>
    <p>Sie wurden erfolgreich eingeloggt.</p>
    Sollte die gewünschte Seite nicht automatisch laden,
    <p><a href="{$component.redirectTo}">clicken Sie bitte hier</a>.</p>
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

    {include file=$component.formTemplate}
{/if}