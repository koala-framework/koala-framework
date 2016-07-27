<table width="600" cellspacing="0" cellpadding="0">
    <?php foreach ($this->paragraphs as $paragraph) { ?>
        <tr>
            <td>
                <?=$this->component($paragraph['data']);?>
            </td>
        </tr>
    <?php } ?>
</table>
