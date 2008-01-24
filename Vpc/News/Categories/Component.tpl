<ul>
    {foreach from=$component.categories item=cat}
        <li>
            <a href="{$cat.href}">{$cat.category}</a>
        </li>
    {/foreach}
</ul>