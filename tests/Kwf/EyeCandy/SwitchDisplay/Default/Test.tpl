<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_EyeCandy_SwitchDisplay_Default'))?>
    </head>
    <body>
        <div class="first">
            <div class="kwfSwitchDisplay">
                <p><a href="#" class="switchLink">1. Foo</a></p>
                <div class="switchContent">Bar</div>
            </div>
        </div>
        <div class="second">
            <div class="kwfSwitchDisplay">
                <p><a href="#" class="switchLink">2. Foo</a></p>
                <div class="switchContent">Bar</div>
            </div>
        </div>
        <div class="third">
            <div class="kwfSwitchDisplay">
                <p><a href="#" class="switchLink">3. Foo</a></p>
                <div class="switchContent">
                    <div class="asyncContent"></div>
                </div>
            </div>
        </div>
        <div id="qunit"></div>
        <div id="qunit-fixture"></div>
    </body>
</html>