<ul id="{$component.level}Menu">
    {foreach from=$component.menu item=m}
    <li class="{$m.class}">
        <a href="{$m.href}" rel="{$m.rel}"><span>{$m.text}</span></a>
        <div class="clear"></div>
    </li>
    {/foreach}
</ul>