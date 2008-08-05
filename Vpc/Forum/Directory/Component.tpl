<div class="vpcForum">
    <form class="forumSearch" method="GET" action="{$component.searchUrl}">
        <span>Forumsuche: </span>
        <input type="text" name="search" value="" />
        <button type="submit"></button>
    </form>
    <?= $this->partial($this->groupsTemplate, array('groups' => $this->groups, 'groupsTemplate' => $this->groupsTemplate)) ?>
    {include file=$component.groupsTemplate groups=$component.groups}
</div>
