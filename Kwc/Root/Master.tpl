<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->includeCode('header')?>
    </head>
    <body>
        Default-Master, override in Root-Komponente.
        <?
        foreach($this->boxes as $box) {
            echo $this->component($box);
        }
        echo $this->componentWithMaster($this->componentWithMaster);
        ?>
        <?=$this->includeCode('footer')?>
    </body>
</html>
