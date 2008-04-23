{if isset($item.preHtml)}{$item.preHtml}{/if}
{if isset($item.html)}
    {$item.html}
{elseif isset($item.items)}
    {foreach from=$item.items item=i}
        {include file="`$smarty.const.VPS_PATH`/Vpc/Formular/field.tpl" item=$i}
    {/foreach}
{elseif isset($item.component)}
    {component component=$item.component}
{/if}
{if isset($item.postHtml)}{$item.postHtml}{/if}
