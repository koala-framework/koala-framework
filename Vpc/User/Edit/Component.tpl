<div class="vpcUserEdit">
    <h1>Konto - Einstellungen</h1>

    {if $component.sent != 3}
        <label>E-Mail Adresse</label>
        <div class="showField">{$component.email}</div>
    {/if}

    {include file=$component.formTemplate}
</div>