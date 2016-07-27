<div class="<?=$this->rootElementClass?>">
    <h2><?=$this->data->trlKwf('Favourites');?></h2>
    <?php if ($this->favourites) { ?>
        <ul>
            <?php foreach ($this->favourites as $favourite) { ?>
                <li class="favourite">
                    <?= $this->componentLink($favourite); ?>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <?=$this->data->trlKwf('You have no favourites!'); ?>
    <?php } ?>
</div>
