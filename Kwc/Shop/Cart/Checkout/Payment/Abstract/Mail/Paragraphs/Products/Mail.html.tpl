<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <?=$this->data->trlpKwf('You ordered the following product', 'You ordered the following products', count($this->items));?>:
        </td>
    </tr>
</table>
<table width="100%" class="tblBoxCart" cellspacing="0" cellpadding="0">
    <?php
    $maxAddOrderData = 0;
    foreach ($this->items as $item) {
        $maxAddOrderData = max($maxAddOrderData, count($item->additionalOrderData));
    }
    $c=0;
    foreach ($this->items as $item) { ?>
        <tr class="products<?=($c%2==0 ? ' row1' : ' row2');?>">
            <td class="product"><?=Kwf_Util_HtmlSpecialChars::filter($item->text);?></td>
            <?php foreach ($item->additionalOrderData as $d) { ?>
                <td class="<?=$d['class']?>"><?=Kwf_Util_HtmlSpecialChars::filter($this->data->trlStaticExecute($d['name']));?>: <?=Kwf_Util_HtmlSpecialChars::filter($this->data->trlStaticExecute($d['value']));?></td>
            <?php } ?>
            <td class="price" colspan="<?=($maxAddOrderData-count($item->additionalOrderData)+1)?>" align="right"><?=$this->money($item->price);?></td>
        </tr>
        <?php $c++;
    } ?>
</table>
<hr width="100%" align="left"/>
<table width="100%" class="moneyInfo" cellspacing="0" cellpadding="0">
    <?php foreach ($this->sumRows as $row) { ?>
        <tr>
            <td align="right">
                <?php
                    if (isset($row['class']) && $row['class']=='valueOfGoods') {
                        echo '<i>'.Kwf_Util_HtmlSpecialChars::filter($this->data->trlStaticExecute($row['text'])).'</i>';
                    } else if (isset($row['class']) && $row['class']=='totalAmount') {
                        echo '<b>'.Kwf_Util_HtmlSpecialChars::filter($this->data->trlStaticExecute($row['text'])).'</b>';
                    } else {
                        echo Kwf_Util_HtmlSpecialChars::filter($this->data->trlStaticExecute($row['text']));
                    }
                ?>
            </td>
            <td width="120" align="right">
                <?php
                    if (isset($row['class']) && $row['class']=='valueOfGoods') {
                        echo '<i>'.$this->money($row['amount'],'').'</i>';
                    } else if (isset($row['class']) && $row['class']=='totalAmount') {
                        echo '<b>'.$this->money($row['amount'],'').'</b>';
                    } else {
                        echo $this->money($row['amount'],'');
                    }
                ?>
            </td>
        </tr>
    <?php } ?>
</table>
