<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Kwc_Favourites:Test')?>
        <?=$this->debugData()?>
    </head>
    <body>
        <div class="box">
            <?=$this->component($this->boxes['favouritesbox'])?>
        </div>
        <?
        echo $this->componentWithMaster($this->componentWithMaster);
        ?>
    </body>
</html>
