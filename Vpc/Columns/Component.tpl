<div class="<?=$this->cssClass ?>">
    <? foreach($this->columns as $c) { ?>
        <div class="column" style="width: <?=$c->row->width?>">
            <?=$this->component($c)?>
        </div>
    <? } ?>
</div>