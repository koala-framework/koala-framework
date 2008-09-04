<div class="<?=$this->cssClass?>">
    <?=$this->component($this->breadCrumbs)?>
    <? if (!$this->isSaved) echo $this->component($this->preview); ?>
    <?=$this->component($this->form)?>
    <?php if (!$this->isSaved) { ?>
    <h2><?=trlVps('Last Posts')?>:</h2>
    <?=$this->component($this->lastPosts)?>
    <?php } ?>
</div>