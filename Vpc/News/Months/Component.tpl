<ul class="newsCatagory">
    {foreach from=$component.months item=month}
        <li>
            <a href="{$month.href}">{$month.monthName} {$month.year}</a>
        </li>
    {/foreach}
</ul>