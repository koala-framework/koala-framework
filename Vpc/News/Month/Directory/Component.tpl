<div class="<?=$this->cssClass?>">
    <ul>
    <? foreach ($this->months as $c) { ?>
        <li><?=$this->componentLink($c)?></li>
    <? } ?>
    </ul>
</div>