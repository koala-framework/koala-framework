<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_Js_OnContentReady', 'Page2'))?>
    </head>
    <body>
        <a href="#" id="hide">hide</a>
        <a href="#" id="show">show</a>

        <div class="foo">foo</div>

        <div id="log"></div>
    </body>
</html>
