<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_FavouritesSelenium', 'TestFiles', 'Kwc_FavouritesSelenium_Root'))?>
        <?=$this->debugData()?>
    </head>
    <body>
        <div class="box">
            <?=$this->component($this->boxes['favouritesbox'])?>
        </div>
        <?php
        echo $this->componentWithMaster($this->componentWithMaster);
        ?>
    </body>
</html>
