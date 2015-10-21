<div class="<?=$this->cssClass?>">
    <h1><?=$this->placeholder['headline'];?></h1><br />
    <form action="<?= $this->data->url ?>" method="post">
        <?=$this->component($this->view)?>
        <div class="clear"></div>
        <? foreach ($this->sumRows as $row) { ?>
            <div class="amounts <?=isset($row['class']) ? $row['class'] : ''?>">
                <span class="label"><?=$this->data->trlStaticExecute($row['text'])?></span>
                <span class="sum"><?=$this->money($row['amount'],'')?></span>
            </div>
        <? } ?>
        <div class="back">
            <?=$this->componentLink($this->shop, $this->placeholder['backToShop']);?>
        </div>

        <div class="submitWrapper <?=$this->buttonClass?>">
            <div class="beforeButton"></div>
            <div class="button">
                <div class="saving"></div>
                <button class="submit" type="submit" name="<?= $this->form->componentId ?>" value="submit">
                    <span><?= $this->data->trlKwf('Update') ?></span>
                </button>
            </div>
            <div class="afterButton"></div>
            <div class="clear"></div>
        </div>

        <? if($this->countProducts) { ?>
            <div class="checkout">
                <?=$this->componentLink($this->checkout, $this->placeholder['checkout'])?>
            </div>
        <? } ?>
        <input type="hidden" name="<?= $this->form->componentId ?>-post" value="post" />
        <div class="clear"></div>
    </form>
</div>
