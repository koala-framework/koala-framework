<div class="<?=$this->cssClass?>">
    <? foreach ($this->languages as $l) { ?>
        <?=$this->componentLink($l['home'], $l['language'])?>
    <? } ?>
</div>