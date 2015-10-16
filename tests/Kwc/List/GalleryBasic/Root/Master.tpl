<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_List_GalleryBasic', 'TestFiles', 'Kwc_List_GalleryBasic_Root'))?>
        <?=$this->debugData()?>
    </head>
    <body><div class="outerContent" style="max-width: 1000px; margin: 0 auto;"><?=$this->componentWithMaster($this->componentWithMaster)?></div></body>
</html>
