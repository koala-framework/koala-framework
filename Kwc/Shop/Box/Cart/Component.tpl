<div class="<?=$this->rootElementClass?>" data-component-id="<?=$this->data->componentId?>">
    <div class="cartList">
        <h2><?=$this->data->trlKwf('Cart')?></h2>
        <?php if (!$this->items) { ?>
            <p><?=$this->data->trlKwf('Cart is empty')?></p>
    </div>
        <?php } else { ?>
            <div class="countProducts">
                <p>
                    <?=$this->data->trlKwf('You cart contains {0} products','<strong>'.$this->order->getTotalAmount().'</strong>')?>
                </p>
            </div>
            <div class="tblBoxCart" cellspacing="0" cellpadding="0">
            <?php
            $c=1;
            $j=count($this->items);
            foreach ($this->items as $item) { ?>
                <ul class="products<?=($c%2==0 ? ' row2' : ' row1');?>">
                    <li class="product"><?=$this->componentLink($item->product, $item->text)?></li>
                    <?php foreach($item->additionalOrderData as $d) { ?>
                        <li class="<?=$d['class']?>"><?=$this->data->trlStaticExecute($d['name'])?>: <?=$d['value']?></li>
                    <?php } ?>
                    <li class="price"><?=$this->money($item->price)?></li>
                    <div class="kwfUp-clear"></div>
                </ul>
                <ul class="<?=($c==$j ? 'lastline' : 'line');?>">
                    <li colspan="<?=(4+count($item->additionalOrderData))?>">
                        <div class="line"></div>
                    </li>
                </ul>
                <?php $c++;
            } ?>
            </div>
            <ul class="moneyInfo kwfUp-webListNone">
                <?php foreach ($this->sumRows as $row) { ?>
                    <li<?php if(isset($row['class'])) {?> class="<?=$row['class']?>"<?php } ?>>
                        <span class="text"><?=$this->data->trlStaticExecute($row['text'])?></span>
                        <span class="price"><?=$this->money($row['amount'],'')?></span>
                        <div class="kwfUp-clear"></div>
                    </li>
                <?php } ?>
            </ul>
    </div>
    <ul class="links">
        <?php foreach ($this->links as $link) { ?>
            <?php if ($link['component']) { ?>
            <li><?=$this->componentLink($link['component'], $this->data->trlStaticExecute($link['text']))?></li>
            <?php } ?>
        <?php } ?>
    </ul>
    <?php } ?>
</div>
