<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Your cart contains');?></h1><br />
    <form action="<?= $this->data->url ?>" method="post">
        <?=$this->component($this->view)?>
        <div class="clear"></div>
        <div class="back"><?=$this->componentLink($this->data->getParentPage(), $this->placeholder['backToShop']);?></div>
        <div class="submitWrapper">
            <span class="beforeButton"></span><span class="button"><button class="submit" type="submit" name="<?= $this->form->componentId ?>" value="submit"><?= trlVps('Update') ?></button></span><span class="afterButton"></span>
            <div class="clear"></div>
        </div>
        <? if($this->countProducts) { ?>
            <div class="checkout"><?=$this->componentLink($this->checkout, $this->placeholder['checkout'])?></div>
        <? } ?>
        <input type="hidden" name="<?= $this->form->componentId ?>-post" value="post" />
        <div class="clear"></div>
    </form>

</div>