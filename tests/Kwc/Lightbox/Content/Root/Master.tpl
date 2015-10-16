<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_Lightbox_Content', 'TestFiles', 'Kwc_Lightbox_Content_Root'))?>
        <?=$this->debugData()?>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    </head>
    <body>
        <div class="innerBody">
            <?=$this->componentWithMaster($this->componentWithMaster)?>
        </div>
    </body>
</html>
