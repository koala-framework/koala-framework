<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_EyeCandy_SwitchDisplay_Custom'))?>
    </head>
    <body>
        <div class="first">
            <div class="customClass">
                <p><a href="#" class="customLink">1. Custom Foo</a></p>
                <div class="customContent">Custom Bar</div>
            </div>
        </div>
        <div id="qunit"></div>
        <div id="qunit-fixture"></div>
    </body>
</html>