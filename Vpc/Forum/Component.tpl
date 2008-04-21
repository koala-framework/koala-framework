<div class="vpcForum">
    <form class="forumSearch" method="GET" action="{$component.searchUrl}">
        <input type="text" name="search" value="" />
        <button type="submit">Im Forum suchen</button>
    </form>
    {include file=$component.groupsTemplate groups=$component.groups}
</div>
