<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Vps_Form_FieldSet_Frontend:TestFrontend')?>
    </head>
    <body>
        <?
        foreach($this->boxes as $box) {
            echo $this->component($box);
        }
        echo $this->componentWithMaster($this->componentWithMaster);
        ?>
    </body>
</html>
