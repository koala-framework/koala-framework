<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Kwc_Basic_ImageEnlarge:Test')?>
        <?=$this->debugData()?>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    </head>
    <body><?=$this->componentWithMaster($this->componentWithMaster)?></body>
</html>
