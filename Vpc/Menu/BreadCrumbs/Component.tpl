<? if ($this->links) { ?>
<h2 class="<?=$this->cssClass?>">
    <? $i = 0;
    foreach($this->links as $l) { ?>
        <?=$this->componentLink($l)?>
        <? if($i < count($this->links)-1) { ?><?=$this->separator?><? } ?>
    <? $i++;
    } ?>
</h2>
<? } ?>