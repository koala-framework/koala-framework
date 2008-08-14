<div class="<?=$this->cssClass?>">
    <?=$this->component($this->paging)?>
    <? foreach($this->items as $post) { ?>
        <?=$this->component($post)?>
    <? } ?>
    <?=$this->component($this->paging)?>
    <div class="post">
        <?=$this->componentLink($this->write, trlVps('add Comment'))?>
    </div>
</div>