<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Vps_EyeCandy_List_Plugins_Hover:Test')?>
        <style type="text/css">
            .testItem { margin: 20px; padding: 10px; background-color: #6be; }
        </style>
    </head>
    <body>
        <div class="testItemWrapper">
            <div id="ti1" class="testItem">Item 1</div>
            <div id="ti2" class="testItem">Item 2</div>
        </div>

        <div>
            <h3>Result:</h3>
            <div id="result"></div>
        </div>
    </body>
</html>
