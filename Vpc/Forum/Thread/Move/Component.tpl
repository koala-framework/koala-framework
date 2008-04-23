<div class="forumMoveThread">
    <h1>Thema verschieben</h1>

    {if $component.threadMoved}
        <p>
            Das Thema wurde erfolgreich verschoben.<br /><br />
            <a href="{$component.groupUrl}">Klicken Sie hier</a>, um zurück zur Gruppe zu gelangen.
        </p>
    {else}
        <p>Bitte klicken Sie auf die Gruppe, in die folgendest Thema verschoben werden soll:</p>

        <h3>{$component.threadVars.subject}</h3>

        {if $component.groupsTemplate}
            {include file=$component.groupsTemplate groups=$component.groups}
        {/if}
    {/if}
</div>
