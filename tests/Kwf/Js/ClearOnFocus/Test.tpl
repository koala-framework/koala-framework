<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head><?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_Js_ClearOnFocus'))?></head>
    <body>
        <input type="text" class="test1 kwfClearOnFocus" value="foo" />
        <input type="text" class="test2" value="foo" />
        <textarea class="test3 kwfClearOnFocus">foo</textarea>
        <textarea class="test4">foo</textarea>
    </body>
</html>
