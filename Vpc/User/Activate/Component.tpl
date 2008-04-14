<h1>{trlVps text="Activate Useraccount"}</h1>

{if $component.sent != 3}
    <p>
    	{trlVps text="Plese enter in both fields the password which you want to use for your useraccount"}.<br />
        {trlVps text="After the activation you are automatically loggid and you could use your account."}
    </p>
{/if}

{include file=$component.formTemplate}