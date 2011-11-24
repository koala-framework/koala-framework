<div class="<?=$this->cssClass?>">
    <? if ($this->hasContent) { ?>
    <ul class="links">
        <? foreach ($this->links as $link) { ?>
            <li><?=$this->componentLink($link['component'], $link['text'])?></li>
        <? } ?>
    </ul>
    <? } ?>
</div>