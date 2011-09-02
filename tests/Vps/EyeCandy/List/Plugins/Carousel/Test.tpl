<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Vps_EyeCandy_List_Plugins_Carousel:Test')?>
        <style type="text/css">
            .testItem { margin: 20px; padding: 10px; background-color: #6be; width: 100px; height: 100px; float: left; }
        </style>
    </head>
    <body>
        <div class="testItemWrapper">
            <div id="ti1" class="testItem">Item 1</div>
            <div id="ti2" class="testItem">Item 2</div>
            <div id="ti3" class="testItem">Item 3</div>
            <div id="ti4" class="testItem">Item 4</div>
            <div id="ti5" class="testItem">Item 5</div>
            <div id="ti6" class="testItem">Item 6</div>
        </div>
    </body>
</html>
