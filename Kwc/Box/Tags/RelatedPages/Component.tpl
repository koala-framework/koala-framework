<div class="<?=$this->rootElementClass?>">
    <? if($this->placeholder['headline']) {?>
    <h1><?=$this->placeholder['headline']?></h1>
    <? } ?>
    <ul>
    <? foreach($this->related as $c) { ?>
        <li><?=$this->componentLink($c)?></li>
    <? } ?>
    </ul>
</div>
