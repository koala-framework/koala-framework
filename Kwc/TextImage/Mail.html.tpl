<table width="100%" cellspacing="0" cellpadding="0">
    <?php if ($this->position == 'center') { ?>
        <?php if ($this->image) { ?>
            <tr>
                <td valign="top"><?=$this->component($this->image);?></td>
            </tr>
            <tr><td height="10" style="font-size: 10px; line-height: 10px">&nbsp;</td></tr>
        <?php } ?>
        <tr>
            <td valign="top"><?=$this->component($this->text);?></td>
        </tr>
    <?php } else if($this->position=='left') { ?>
        <tr>
            <?php if ($this->image) { ?>
                <td valign="top"><?=$this->component($this->image);?></td>
                <td width="10">&nbsp;</td>
            <?php } ?>
            <td valign="top"><?=$this->component($this->text);?></td>
        </tr>
    <?php } else { ?>
        <tr>
            <td valign="top"><?=$this->component($this->text);?></td>
            <?php if ($this->image) { ?>
                <td width="10">&nbsp;</td>
                <td valign="top" align="right"><?=$this->component($this->image);?></td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>
