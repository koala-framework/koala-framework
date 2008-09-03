<div class="<?=$this->cssClass?>">
    <div class="forumSearch">
        <?=$this->component($this->searchForm)?>
    </div>
    <?= $this->partial($this->groupsTemplate, array('groups' => $this->groups, 'groupsTemplate' => $this->groupsTemplate)) ?>
</div>
