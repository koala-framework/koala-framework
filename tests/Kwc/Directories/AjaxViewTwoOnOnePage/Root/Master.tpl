<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Kwc_Directories_AjaxViewTwoOnOnePage:Test')?>
        <?=$this->debugData()?>
    </head>
    <body>
        <?
        echo $this->componentWithMaster($this->componentWithMaster);
        ?>
    </body>
</html>
