<h2 class="<?=$this->cssClass?>">
    <? foreach($this->links as $i=>$l) { ?>
        <?=$this->componentLink($l)?>
        <? if($i < count($this->links)-1) { ?><?=$this->separator?><? } ?>
    <? } ?>
</h2>