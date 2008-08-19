<div class="<?=$this->cssClass?>">
    <div class="forumSearch">
        <form class="forumSearch" method="GET" action="{$component.searchUrl}">
            <span>Forumsuche: </span>
            <input type="text" name="search" value="" />
            <button type="submit"><?=$this->placeholder['searchButtonText']?></button>
        </form>
    </div>
    <?= $this->partial($this->groupsTemplate, array('groups' => $this->groups, 'groupsTemplate' => $this->groupsTemplate)) ?>
</div>
