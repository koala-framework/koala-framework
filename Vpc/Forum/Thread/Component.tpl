
{if $component.moveUrl || $component.closeToggleUrl}
    <fieldset>
        <legend>Thema moderieren</legend>

        <a href="{$component.moveUrl}">Thema verschieben</a>
        <a href="{$component.closeToggleUrl}">{if $component.closeToggleCurrent}Thema reaktivieren{else}Thema schließen{/if}</a>

    </fieldset>
{/if}

{component component=$component.posts}

