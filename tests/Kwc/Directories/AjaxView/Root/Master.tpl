<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_Directories_AjaxView', 'TestFiles', 'Kwc_Directories_AjaxView_Root'))?>
        <?=$this->debugData()?>
    </head>
    <body>
        <div class="menu">
            <?=$this->component($this->boxes['menu'])?>
        </div>
        <?
        echo $this->componentWithMaster($this->componentWithMaster);
        ?>
    </body>
</html>
