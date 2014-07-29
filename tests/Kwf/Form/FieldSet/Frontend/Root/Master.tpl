<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_Form_FieldSet_Frontend', 'TestFiles', 'Kwf_Form_FieldSet_Frontend_Root'))?>
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
