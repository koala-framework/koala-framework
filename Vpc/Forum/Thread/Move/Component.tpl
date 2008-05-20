<div class="forumMoveThread">
    <h1>Thema verschieben</h1>

    {if $component.threadMoved}
        <p class="massage">Das Thema wurde erfolgreich verschoben.</p>
        <p>
            <a href="{$component.groupUrl}">Klicken Sie hier</a>, um zurück zur Gruppe zu gelangen.
        </p>
    {else}
        <h3>{$component.threadVars.subject}</h3>
        <p>Bitte klicken Sie auf die Gruppe, in die das Thema verschoben werden soll:</p>

        {if $component.groupsTemplate}
            {include file=$component.groupsTemplate groups=$component.groups}
        {/if}
    {/if}
</div>
