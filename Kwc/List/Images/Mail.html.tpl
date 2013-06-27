<table cellspacing="0" cellpadding="0">
    <tr>
        <?
        $cnt = count($this->children);
        $i = 1;
        foreach ($this->children as $child) { ?>
            <td><?= $this->component($child); ?></td>
            <? if($i!=$cnt) { ?>
                <td width="10">&nbsp;</td>
            <? }
            $i++;
        } ?>
    </tr>
</table>
