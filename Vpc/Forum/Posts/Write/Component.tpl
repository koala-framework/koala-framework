<div class="<?=$this->cssClass?>">
    <?=$this->component($this->breadCrumbs)?>
    <?=$this->component($this->form)?>
    <?php if (!$this->isSaved) { ?>
    <h2><?=trlVps('Last Posts')?>:</h2>
    <?=$this->component($this->lastPosts)?>
    <?php } ?>
</div>