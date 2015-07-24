<div class="<?=$this->rootElementClass?>">
    <table class="<?=$this->tableStyle?>" cellspacing="0" cellpadding="0">
        <? if ($this->headerRows) { ?>
        <thead>
            <? foreach ($this->headerRows as $dr) { ?>
            <tr class="<?=$dr['cssClass']; ?>">
                <? foreach ($dr['data'] as $dataItem) { ?>
                <<?=$dr['htmlTag'];?> class="<?= $dataItem['cssClass']; ?>">
                    <?= $this->toHtmlLink($dataItem['value']); ?>
                </<?=$dr['htmlTag'];?>>
                <? } ?>
            </tr>
            <? } ?>
        </thead>
        <tbody>
        <? } ?>
            <? foreach ($this->dataRows as $dr) { ?>
            <tr class="<?=$dr['cssClass']; ?>">
                <? foreach ($dr['data'] as $dataItem) { ?>
                <<?=$dr['htmlTag'];?> class="<?= $dataItem['cssClass']; ?>">
                    <?= $this->toHtmlLink($dataItem['value']); ?>
                </<?=$dr['htmlTag'];?>>
                <? } ?>
            </tr>
            <? } ?>
        <? if ($this->headerRows) { ?>
        </tbody>
        <? } ?>
    </table>
</div>
