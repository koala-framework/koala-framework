<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Kwf_Js_Resizer:Test')?>
    </head>
    <body>
        <div class="kwfResizeElement">
            <div class="constraintBox" style="width:200px;height:200px;background-color:yellow;margin-left:50px;margin-top:50px;">
                <div class="resizeElement" style="width:50px;height:50px;background-color:orange">
                </div>
            </div>
        </div>
    </body>
</html>
