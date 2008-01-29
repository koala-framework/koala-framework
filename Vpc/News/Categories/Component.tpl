<ul class="newsCatagory">
    {foreach from=$component.categories item=cat}
        <li>
            <a href="{$cat.href}">{$cat.value}</a>
        </li>
    {/foreach}
</ul>