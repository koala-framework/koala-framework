<ul id="{$component.level}Menu">
    {foreach from=$component.menu item=m}
    <li class="{$m.class}">
        <a href="{$m.href}" rel="{$m.rel}"><span>{$m.text}</span></a>
        <div class="clear"></div>
        {if $m.submenu}
            <div class="{$component.level}Submenu">
            {strip}
            <ul>
            {foreach from=$m.submenu item=sm}
                <li class="{$sm.class}">
                <a href="{$sm.href}" rel="{$m.rel}"><span>{$sm.text}</span></a>
                </li>
            {/foreach}
            </ul>
            {/strip}
            </div>
        {/if}
    </li>
    {/foreach}
</ul>