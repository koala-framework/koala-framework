<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new new Kwf_Assets_Package_TestPackage('Kwc_Columns_Basic', 'TestFiles', 'Kwc_Columns_Basic_Root'))?>
        <?=$this->debugData()?>
    </head>
    <body><?=$this->componentWithMaster($this->componentWithMaster)?></body>
</html>
