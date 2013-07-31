<?=$this->doctype('XHTML1_STRICT');?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->includeCode('header')?>
    </head>
    <body class="<?=$this->cssClass?>">
        <div id="absoluteBg"></div>
        <div id="page">
            <div id="outerHeader">
                <div id="header">
                    <div id="mainMenu">
                        <?=$this->component($this->boxes['mainMenu']);?>
                    </div>
                    <div id="social">
                        <div id="twitter"></div>
                        <div id="facebook"></div>
                        <div id="linkedin"></div>
                    </div>
                </div>
            </div>
            <div id="outerContent">
                <div id="content">
                    <div class="listFade">
                        <?=$this->component($this->boxes['listFade']);?>
                    </div>
                    <div id="innerContent">
                        <div id="leftColumn">
                            <div style="width: <?=$this->componentWidth($this->data)?>px">
                                <?=$this->componentWithMaster($this->componentWithMaster);?>
                            </div>
                        </div>
                        <div id="rightColumn">
                        </div>
                    </div>
                </div>
                <div id="footer">
                    <?=$this->component($this->boxes['bottomMenu']);?>
                </div>
            </div>
        </div>
        <?=$this->includeCode('footer')?>
    </body>
</html>
