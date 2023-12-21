<div class="<?=$this->rootElementClass?>">
    <table class="<?=$this->tableStyle?>" cellspacing="0" cellpadding="0">
        <?php if ($this->headerRows) { ?>
        <thead>
            <?php foreach ($this->headerRows as $dr) { ?>
            <tr class="<?=$dr['cssClass']; ?>">
                <?php foreach ($dr['data'] as $dataItem) { ?>
                <<?=$dr['htmlTag'];?> class="<?= $dataItem['cssClass']; ?>">
                    <?= $this->toHtmlLink($dataItem['value']); ?>
                </<?=$dr['htmlTag'];?>>
                <?php } ?>
            </tr>
            <?php } ?>
        </thead>
        <tbody>
        <?php } ?>
            <?php foreach ($this->dataRows as $dr) { ?>
            <tr class="<?=$dr['cssClass']; ?>">
                <?php foreach ($dr['data'] as $dataItem) { ?>
                <<?=$dr['htmlTag'];?> class="<?= $dataItem['cssClass']; ?>">
                    <?= $this->toHtmlLink(strip_tags($dataItem['value'])); ?>
                </<?=$dr['htmlTag'];?>>
                <?php } ?>
            </tr>
            <?php } ?>
        <?php if ($this->headerRows) { ?>
        </tbody>
        <?php } ?>
    </table>
</div>
