<div class="<?=$this->cssClass?>">
    <table class="<? if (!empty($this->settingsRow->table_style)) echo $this->settingsRow->table_style; ?>">
        <? foreach ($this->dataRows as $k => $dr) { ?>
            <tr class="<?= $k%2 == 0 ? 'odd' : 'even'; ?> <? if (!empty($dr->css_style)) echo $dr->css_style; ?>">
                <? for ($i = 1; $i <= $this->settingsRow->columns; $i++) {
                    $tag = 'td';
                    if (!empty($dr->css_style)) {
                        $rowStyles = Vpc_Abstract::getSetting($this->data->componentClass, 'rowStyles');
                        if (is_array($rowStyles[$dr->css_style]) && !empty($rowStyles[$dr->css_style]['tag'])) {
                            $tag = $rowStyles[$dr->css_style]['tag'];
                        }
                    }
                ?>
                    <<?=$tag;?> class="col<?= $i; ?>"><?= $dr->{'column'.$i}; ?></<?=$tag;?>>
                <? } ?>
            </tr>
        <? } ?>
    </table>
</div>
