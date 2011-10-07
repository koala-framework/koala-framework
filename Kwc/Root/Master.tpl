<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Frontend')?>
        <?=$this->debugData()?>
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
