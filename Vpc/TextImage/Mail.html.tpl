<table width="100%" cellspacing="0" cellpadding="0">
    <? if($this->position=='left') { ?>
        <tr>
            <? if ($this->image) { ?>
                <td valign="top"><?=$this->component($this->image);?></td>
                <td width="10">&nbsp;</td>
            <? } ?>
            <td valign="top"><?=$this->component($this->text);?></td>
        </tr>
    <? } else { ?>
        <tr>
            <td valign="top"><?=$this->component($this->text);?></td>
            <? if ($this->image) { ?>
                <td width="10">&nbsp;</td>
                <td valign="top" align="right"><?=$this->component($this->image);?></td>
            <? } ?>
        </tr>
    <? } ?>
</table>
