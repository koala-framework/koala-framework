<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <<?=$this->headlineType['tag'] ?><?php if ($this->headlineType['class']) { ?> class="<?=$this->headlineType['class']?>"<?php } ?>>
                <?= $this->headline1 ?>
            </<?=$this->headlineType['tag'] ?>>
        </td>
    </tr>
     <?php if ($this->headline2) { ?>
    <tr>
        <td>
            <span class="subHeadline"><?= $this->headline2 ?></span>
        </td>
    </tr>
    <?php } ?>
</table>
