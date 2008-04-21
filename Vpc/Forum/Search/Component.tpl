<div class="vpcForum">
    <form class="forumSearch" method="GET" action="{$component.action}">
        <input type="text" name="search" value="{$component.searchText}" />
        <button type="submit">Im Forum suchen</button>
    </form>
    {component component=$component.paging}
    <ul class="forumResults">
    {foreach from=$component.results item=r}
        <li><a href="{$r.href}" rel="{$r.rel}">{$r.subject|htmlspecialchars}</a></li>
    {/foreach}
    </ul>
</div>
