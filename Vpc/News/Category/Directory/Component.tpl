<div class="<?=$this->cssClass?>">
    <ul>
    <? foreach ($this->categories as $c) { ?>
        <li><?=$this->componentLink($c)?></li>
    <? } ?>
    </ul>
</div>