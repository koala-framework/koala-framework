<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_Js_OnContentReady', 'Page1'))?>
    </head>
    <body class="frontend">
        <div class="foo">foo</div>
    </body>
</html>
