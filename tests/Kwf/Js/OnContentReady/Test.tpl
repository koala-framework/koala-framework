<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_Js_OnContentReady'))?>
    </head>
    <body>
        <div id="iframe-container"></div>
        <div id="qunit"></div>
        <div id="qunit-fixture"></div>
    </body>
</html>
