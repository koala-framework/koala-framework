<div class="<?=$this->cssClass?>">
    <? foreach($this->items as $post) { ?>
        <?=$this->component($post)?>
    <? } ?>
    <?=$this->componentLink($this->write, trlVps('add Comment'))?>

    <?=$this->component($this->paging)?>
</div>