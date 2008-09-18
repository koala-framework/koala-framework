<div class="<?=$this->cssClass?>">
    <? if (!$this->isSaved) echo $this->component($this->preview); ?>
    <?=$this->component($this->form)?>
    <? if (!$this->isSaved) { ?>
    <h1 class="mainHeadline"><?=trlVps('Last Posts')?>:</h1>
    <?=$this->component($this->lastPosts)?>
    <? } ?>
</div>