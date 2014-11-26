<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_Basic_ImageEnlarge', 'TestFiles', 'Kwc_Basic_ImageEnlarge_Root'))?>
        <?=$this->debugData()?>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    </head>
    <body><?=$this->componentWithMaster($this->componentWithMaster)?></body>
</html>
