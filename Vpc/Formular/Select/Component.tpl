{if $component.type == 'select'}
    <select name="{$component.name}"{if $component.width} style="width:{$component.width}px;"{/if}>
        {foreach from=$component.options item=option}
            <option value="{$option.value}"{if $option.checked} selected="selected"{/if}>{$option.text}</option>
        {/foreach}
    </select>
{else}
    {foreach from=$component.options item=option}
    <input type="radio" name="{$component.name}" value="{$option.value}" {if $option.checked}checked{/if}/>{$option.text} {if $component.type != 'radio_horizontal'} <br> {/if}
    {/foreach}
{/if}
