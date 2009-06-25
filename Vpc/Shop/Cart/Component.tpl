<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Your cart contains');?></h1><br />
    <form action="<?= $this->data->url ?>" method="post">
        <?=$this->component($this->view)?>
        <div class="back"><?=$this->componentLink($this->data->getParentPage(),trlVps('Back to shop'));?></div>
        <div class="submitWrapper">
            <span class="beforeButton"></span><span class="button"><button class="submit" type="submit" name="<?= $this->form->componentId ?>" value="submit"><?= trlVps('Update') ?></button></span><span class="afterButton"></span>
            <div class="clear"></div>
        </div>
        <input type="hidden" name="<?= $this->form->componentId ?>-post" value="post" />
    </form>
    <? if($this->countProducts) { ?>
    <div class="checkout"><?=$this->componentLink($this->checkout, trlVps('To checkout'))?></div>
    <? } ?>

</div>