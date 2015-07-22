<div class="<?=$this->rootElementClass?>">
    <ul>
        <? $i=0; foreach($this->links as $l) { ?>
            <li<? if ($i == 0) { echo ' class="first"'; }?>>
                <?=$this->componentLink($l)?>
            </li>
        <? $i++; } ?>
    </ul>
</div>
