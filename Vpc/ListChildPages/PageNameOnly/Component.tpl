<div class="<?=$this->cssClass?>">
    <ul>
        <? foreach ($this->childPages as $cp) { ?>
            <li><?= $this->componentLink($cp); ?></li>
        <? } ?>
    </ul>
</div>
