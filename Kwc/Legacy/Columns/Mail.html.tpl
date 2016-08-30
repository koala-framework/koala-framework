<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <?php $i = 1; ?>
    <?php foreach ($this->listItems as $child) { ?>
        <td align="left" width="<?=$child['width']?>">
            <?=$this->component($child['data']);?>
        </td>
        <?php if ($i < count($this->children)) { ?>
            <td width="10">&nbsp;</td>
        <?php } ?>
    <?php $i++; ?>
    <?php } ?>
    </tr>
</table>
