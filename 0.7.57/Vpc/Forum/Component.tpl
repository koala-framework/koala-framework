<div class="vpcForum">
    <form method="GET" action="{$component.searchUrl}">
        <input type="text" name="search" value="" />
        <button type="submit">{trlVps text="Search"}</button>
    </form>
    {include file=$component.groupsTemplate groups=$component.groups}
</div>
