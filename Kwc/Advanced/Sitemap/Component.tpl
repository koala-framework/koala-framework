<div class="<?=$this->cssClass?>">
    <? if ($this->target) { ?>
        <p><?=$this->componentLink($this->target)?></p>
        <?=$this->listHtml?>
    <? } ?>
</div>