<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_ImageResponsive_CreatesImgElement', 'TestFiles', 'Kwc_ImageResponsive_CreatesImgElement_Root_Component'))?>
        <?=$this->debugData()?>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    </head>
    <body><?=$this->componentWithMaster($this->componentWithMaster)?></body>
</html>
