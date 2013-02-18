<div class="<?=$this->cssClass?>">
    <h2><?=$this->data->trl('Favourites');?></h2>
    <? if ($this->favourites) { ?>
        <? foreach ($this->favourites as $favourite) { ?>
            <div class="favourite">
                <?= $this->componentLink($favourite); ?>
            </div>
        <? } ?>
    <? } else { ?>
        <?=$this->data->trl('You have no favourites!'); ?>
    <? } ?>
</div>
