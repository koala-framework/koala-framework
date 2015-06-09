<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_Trl_MenuCache', 'TestFiles', 'Kwc_Trl_MenuCache_Root'))?>
    </head>
    <body>
        Default-Master, in Root-Komponente überschreiben!
        <?
        foreach($this->boxes as $box) {
            echo $this->component($box);
        }
        echo $this->componentWithMaster($this->componentWithMaster);
        ?>
    </body>
</html>
