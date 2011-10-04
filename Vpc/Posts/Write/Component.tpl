<div class="<?=$this->cssClass?>">
    <? if (!$this->isSaved) echo $this->component($this->preview); ?>
    <?=$this->component($this->form)?>
    <? if (!$this->isSaved) { ?>
        <?if ($this->hasContent($this->lastPosts)) {?>
            <h1 class="mainHeadline"><?=$this->placeholder['lastPosts']?>:</h1>
            <?=$this->component($this->lastPosts)?>
        <?}?>
    <? } ?>
</div>