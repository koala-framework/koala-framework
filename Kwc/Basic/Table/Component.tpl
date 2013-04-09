<div class="<?=$this->cssClass?>">
    <table class="<? if (!empty($this->settingsRow->table_style)) echo $this->settingsRow->table_style; ?>" cellspacing="0" cellpadding="0">
        <? foreach ($this->dataRows as $dr) { ?>
            <tr class="<?=$dr['cssClass']; ?>">
                <? $tag = $dr['htmlTag']; ?>
                <? foreach ($dr['data'] as $dataItem) { ?>
                    <<?=$tag;?> class="<?= $dataItem['cssClass']; ?>"><?= $this->toHtmlLink($dataItem['value']); ?></<?=$tag;?>>
                <? } ?>
            </tr>
        <? } ?>
    </table>
</div>
