<div class="vpcUserEdit">
    <h1>{trlVps text="Account - Properties"}</h1>

    {if $component.sent != 3}
        <label>{trlVps text="E-Mail Adress"}</label>
        <div class="showField">{$component.email}</div>
    {/if}

    {include file=$component.formTemplate}
</div>