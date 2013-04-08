<div class="<?=$this->cssClass?>">
    <table class="<? if (!empty($this->settingsRow->table_style)) echo $this->settingsRow->table_style; ?>" cellspacing="0" cellpadding="0">
        <? foreach ($this->dataRows as $k => $dr) { ?>
            <tr class="<?= $k%2 == 0 ? 'odd' : 'even'; ?> <? if (!empty($dr['cssStyle'])) echo $dr['cssStyle']; ?>">
                <? for ($i = 1; $i <= $this->columnCount; $i++) {
                    $tag = 'td';
                    if (!empty($dr['cssStyle'])) {
                        if (is_array($this->rowStyles[$dr['cssStyle']]) && !empty($this->rowStyles[$dr['cssStyle']]['tag'])) {
                            $tag = $this->rowStyles[$dr['cssStyle']]['tag'];
                        }
                    }
                ?>
                    <<?=$tag;?> class="col<?= $i; ?><? if($i==1) echo' first';?><? if($i==$this->columnCount) echo' last';?>"><?= $this->toHtmlLink($dr['column'.$i]); ?></<?=$tag;?>>
                <? } ?>
            </tr>
        <? } ?>
    </table>
</div>
