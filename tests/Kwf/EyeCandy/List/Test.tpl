<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_EyeCandy_List'))?>
        <style type="text/css">
            .testItem { margin: 20px; padding: 10px; background-color: #6be; }
        </style>
    </head>
    <body>
        <div class="testItemWrapper">
            <p id="nerv1">Nur ein bisschen Ablenkungstext</p>
            <div id="ti1" class="testItem">Item 1</div>
            <div id="ti2" class="testItem">Item 2</div>
            <p id="nerv2">Zweiter Ablenkungstext</p>
            <div id="ti3" class="testItem">Item 3</div>
            <div id="ti4" class="testItem">Item 4</div>
            <div id="ti5" class="testItem">Item 5</div>
        </div>

        <div>
            <h3>Result:</h3>
            <div id="result"></div>
        </div>
    </body>
</html>
