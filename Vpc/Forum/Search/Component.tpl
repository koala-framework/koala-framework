<form method="GET" action="{$component.action}">
    <input type="text" name="search" value="{$component.searchText}" />
    <button type="submit">{trlVps text="Search"}</button>
</form>
{component component=$component.paging}
<ul>
{foreach from=$component.results item=r}
    <li><a href="{$r.href}" rel="{$r.rel}">{$r.subject|htmlspecialchars}</a></li>
{/foreach}
</ul>
