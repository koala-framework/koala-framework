<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_ImageResponsive_InScrollDiv', 'TestFiles', 'Kwc_ImageResponsive_InScrollDiv_Root_Component'))?>
        <?=$this->debugData()?>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    </head>
    <body>
        <div style="background-color: blue; height: 400px; overflow: scroll;" id="scrollContainer">
            <div style="background-color: red; margin-top: 1000px;">
                <?=$this->componentWithMaster($this->componentWithMaster)?>
            </div>
        </div>
    </body>
</html>
