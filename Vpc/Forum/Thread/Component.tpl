{component component=$component.posts}

<div class="forumMoveThread">
{if $component.moveUrl || $component.closeToggleUrl}
    <fieldset>
        <legend>Thema moderieren</legend>

        <a class="themaVerschieben" href="{$component.moveUrl}">Thema verschieben</a>

        {if $component.closeToggleCurrent}
            <a class="threadOpen" href="{$component.closeToggleUrl}">Thema öffnen</a>
        {else}
            <a class="threadClose" href="{$component.closeToggleUrl}">Thema schließen</a>
        {/if}

    </fieldset>
{/if}
</div>



