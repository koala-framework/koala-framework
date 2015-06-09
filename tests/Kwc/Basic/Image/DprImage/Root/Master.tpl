<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_Basic_Image_DprImage', 'TestFiles', 'Kwc_Basic_Image_DprImage_Root'))?>
        <?=$this->debugData()?>
    </head>
    <body><?=$this->componentWithMaster($this->componentWithMaster)?></body>
</html>
