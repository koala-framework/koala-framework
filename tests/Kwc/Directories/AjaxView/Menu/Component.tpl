<div class="<?=$this->cssClass?>">
    <?=$this->componentLink($this->directory)?>

    <ul>
    <? foreach($this->categories as $c) { ?>
        <li><?=$this->componentLink($c)?></li>
    <? } ?>
    </ul>
</div>