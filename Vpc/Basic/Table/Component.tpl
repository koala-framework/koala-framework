<div class="<?=$this->cssClass?>">
    <table class="<? if (!empty($this->settingsRow->table_style)) echo $this->settingsRow->table_style; ?>" cellspacing="0" cellpadding="0">
        <? foreach ($this->dataRows as $k => $dr) { ?>
            <tr class="<?= $k%2 == 0 ? 'odd' : 'even'; ?> <? if (!empty($dr->css_style)) echo $dr->css_style; ?>">
                <? for ($i = 1; $i <= $this->settingsRow->columns; $i++) {
                    $tag = 'td';
                    if (!empty($dr->css_style)) {
                        if (is_array($this->rowStyles[$dr->css_style]) && !empty($this->rowStyles[$dr->css_style]['tag'])) {
                            $tag = $this->rowStyles[$dr->css_style]['tag'];
                        }
                    }
                ?>
                    <<?=$tag;?> class="col<?= $i; ?><? if($i==1) echo' first';?><? if($i==$this->settingsRow->columns) echo' last';?>"><?= $dr->{'column'.$i}; ?></<?=$tag;?>>
                <? } ?>
            </tr>
        <? } ?>
    </table>
</div>
