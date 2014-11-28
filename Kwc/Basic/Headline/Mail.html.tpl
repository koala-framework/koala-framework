<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <<?=$this->headlineType['tag'] ?><? if ($this->headlineType['class']) { ?> class="<?=$this->headlineType['class']?>"<? } ?>>
                <?= $this->headline1 ?>
            </<?=$this->headlineType['tag'] ?>>
        </td>
    </tr>
     <? if ($this->headline2) { ?>
    <tr>
        <td>
            <span class="subHeadline"><?= $this->headline2 ?></span>
        </td>
    </tr>
    <? } ?>
</table>