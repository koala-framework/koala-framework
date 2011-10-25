<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Kwc_Composite_ImagesEnlarge:TestFrontend')?>
        <?=$this->debugData()?>
    </head>
    <body><?=$this->componentWithMaster($this->componentWithMaster)?></body>
</html>
