<div class="<?=$this->rootElementClass?>">
    <? if ($this->target) { ?>
        <p><?=$this->componentLink($this->target)?></p>
        <?=$this->listHtml?>
    <? } ?>
</div>