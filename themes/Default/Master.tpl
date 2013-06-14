<?=$this->doctype('XHTML1_STRICT');?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->includeCode('header')?>
    </head>
    <body class="frontend">
        <div id="page">
            <div id="outerHeader">
                <div id="header">
                    <div class="logo">
                        <?=$this->component($this->boxes['logo']);?>
                    </div>
                    <div class="searchBox"></div>
                    <div class="mainMenu">
                        <?=$this->component($this->boxes['mainMenu']);?>
                    </div>
                </div>
            </div>
            <div id="outerContent">
                <div class="stage">
                    <?=$this->component($this->boxes['listFade']);?>
                </div>
                <div id="content">
                    <div id="innerContent">
                        <div class="leftColumn">
                            <?=$this->component($this->boxes['subMenu']);?>
                        </div>
                        <div class="centerColumn">
                            <?=$this->component($this->boxes['breadcrumbs']);?>
                            <div style="width: <?=$this->componentWidth($this->data)?>px">
                                <?=$this->componentWithMaster($this->componentWithMaster);?>
                            </div>
                        </div>
                        <div class="rightColumn">
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="bottomStage">
                <div class="bottomStageShadow"></div>
                <div class="innerBottomStage">
                    <?=$this->component($this->boxes['bottomStage']);?>
                </div>
            </div>
            <div id="outerFooter">
                <div id="footer">
                    <div class="loginButton"></div>
                    <div class="bottomMenu">
                        <?=$this->component($this->boxes['bottomMenu']);?>
                    </div>
                    <div class="leftArea"></div>
                </div>
            </div>
        </div>
        <?=$this->includeCode('footer')?>
    </body>
</html>
