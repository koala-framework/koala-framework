<div class="<?=$this->cssClass?>">
    <ul>
    <? foreach ($this->hits as $hit) { ?>
        <li><?=$this->componentLink($hit->data)?></li>
    <? } ?>
    </ul>
</div>