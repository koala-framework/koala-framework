<div class="<?=$this->cssClass?>">
    <form action="<?= $this->data->url ?>" method="post">
        <?=$this->component($this->view)?>
        <div class="submitWrapper">
            <span class="beforeButton"></span><span class="button"><button class="submit" type="submit" name="<?= $this->form->componentId ?>" value="submit"><?= trlVps('Save changes') ?></button></span><span class="afterButton"></span>
            <div class="clear"></div>
        </div>
    </form>

    <? if($this->countProducts) { ?>
    <div class="checkout"><?=$this->componentLink($this->checkout, trlVps('To checkout'))?></div>
    <? } ?>

</div>