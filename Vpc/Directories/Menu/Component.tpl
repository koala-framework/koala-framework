<div class="<?=$this->cssClass?>">
    <? foreach($this->categories as $type=>$links) { ?>
    <h3><?=$type?></h3>
    <ul>
        <? foreach($links as $m) { ?>
            <li class="<?= $m['class'] ?>">
                <?=$this->componentLink($m['data'])?>
            </li>
        <? } ?>
    </ul>
    <? } ?>
</div>