<div class="<?=$this->cssClass?>">
    <h2><?=$this->data->trlKwf('Favourites');?></h2>
    <? if ($this->favourites) { ?>
        <ul>
            <? foreach ($this->favourites as $favourite) { ?>
                <li class="favourite">
                    <?= $this->componentLink($favourite); ?>
                </li>
            <? } ?>
        </ul>
    <? } else { ?>
        <?=$this->data->trlKwf('You have no favourites!'); ?>
    <? } ?>
</div>
