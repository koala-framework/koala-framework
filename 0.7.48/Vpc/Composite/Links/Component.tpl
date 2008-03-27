<ul>
    {foreach from=$component.children item=child}
        <li>{component component=$child}</li>
    {/foreach}
</ul>
