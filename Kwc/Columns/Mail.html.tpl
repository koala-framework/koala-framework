<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <? $i = 1; ?>
    <? foreach ($this->listItems as $child) { ?>
        <td align="left" width="<?=$child['width']?>">
            <?=$this->component($child['data']);?>
        </td>
        <? if ($i < count($this->children)) { ?>
            <td width="10">&nbsp;</td>
        <? } ?>
    <? $i++; ?>
    <? } ?>
    </tr>
</table>