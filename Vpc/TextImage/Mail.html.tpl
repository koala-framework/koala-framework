<table width="100%" cellspacing="0" cellpadding="0">
    <? if($this->position=='left') { ?>
        <tr>
            <? if ($this->image) { ?>
                <td><?=$this->component($this->image)?></td>
            <? } ?>
            <td><?=$this->component($this->text)?></td>
        </tr>
    <? } else { ?>
        <tr>
            <td><?=$this->component($this->text)?></td>
            <? if ($this->image) { ?>
                <td align="right"><?=$this->component($this->image)?></td>
            <? } ?>
        </tr>
    <? } ?>
</table>
