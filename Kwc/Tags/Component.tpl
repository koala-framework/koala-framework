<div class="<?=$this->cssClass?>">
    <h2><?=$this->headline?></h2>
    <div class="tags">
        <?=implode(',', $this->tags)?>
    </div>

    <? if ($this->suggestions) { ?>
        <?=$this->component($this->suggestions)?>
    <? } ?>
</div>
