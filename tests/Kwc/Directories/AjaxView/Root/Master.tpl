<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Kwc_Directories_AjaxView:Test')?>
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
