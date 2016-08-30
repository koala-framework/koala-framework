<div class="<?=$this->rootElementClass?>">
    <h2><?=$this->headline?></h2>
    <div class="tags">
        <?=implode(', ', $this->tags)?>
    </div>

    <?php if ($this->suggestions) { ?>
        <?=$this->component($this->suggestions)?>
    <?php } ?>
</div>
