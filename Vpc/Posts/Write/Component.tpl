<div class="<?=$this->cssClass?>">
    <? if (!$this->isSaved) echo $this->component($this->preview); ?>
    <?=$this->component($this->form)?>
    <? if (!$this->isSaved) { ?>
    <h2><?=trlVps('Last Posts')?>:</h2>
    <?=$this->component($this->lastPosts)?>
    <? } ?>
</div>