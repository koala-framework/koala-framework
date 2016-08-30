<div class="<?=$this->rootElementClass?>">
    <?php if (!$this->isSaved) echo $this->component($this->preview); ?>
    <?=$this->component($this->form)?>
    <?php if (!$this->isSaved) { ?>
        <?php if ($this->hasContent($this->lastPosts)) { ?>
            <h1 class="mainHeadline"><?=$this->placeholder['lastPosts']?>:</h1>
            <?=$this->component($this->lastPosts)?>
        <?php } ?>
    <?php } ?>
</div>
