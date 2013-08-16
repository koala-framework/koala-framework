<div class="<?=$this->cssClass?>">
    <? if ($this->entriesCount) { ?>
        <h2><?=$this->data->trlpKwf('One Thought on "{1}"', '{0} Thoughts on "{1}"', array($this->entriesCount, '<span class=\"name\">'.$this->data->parent->parent->name.'</span>'))?></h2>
    <? } ?>
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <?=$this->partials($this->data);?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>