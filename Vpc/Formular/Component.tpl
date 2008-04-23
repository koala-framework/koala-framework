{if $component.isSuccess}
    {component component=$component.success}
{else}
    {if $component.errors}
        Fehler:
        <ul>
        {foreach from=$component.errors item=e}
            <li>{$e}</li>
        {/foreach}
        </ul>
    {/if}

    <form action="{$component.action}" method="POST">
        {include file="`$smarty.const.VPS_PATH`/Vpc/Formular/field.tpl" item=$component.form}
        <button type="submit" name="{$component.formName}" value="submit">{$component.placeholder.submitButton}</button>
    </form>
{/if}