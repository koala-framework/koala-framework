<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_Directories_AjaxViewTwoOnOnePage', 'TestFiles', 'Kwc_Directories_AjaxViewTwoOnOnePage_Root'))?>
        <?=$this->debugData()?>
    </head>
    <body>
        <?php
        echo $this->componentWithMaster($this->componentWithMaster);
        ?>
    </body>
</html>
