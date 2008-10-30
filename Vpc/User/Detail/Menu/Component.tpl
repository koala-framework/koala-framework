<div class="<?=$this->cssClass?>">
    <?=trlVps('Your options')?>:
    <? foreach($this->links as $l) { ?>
        <?=$this->componentLink($l)?>
    <? } ?>
</div>