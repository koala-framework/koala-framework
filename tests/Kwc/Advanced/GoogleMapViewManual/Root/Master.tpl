<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_Advanced_GoogleMapViewManual'))?>
    <?=$this->debugData()?>
</head>
<body class="kwfUp-frontend">
<?php
        echo $this->componentWithMaster($this->componentWithMaster);
?>
</body>
</html>
