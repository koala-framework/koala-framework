<?=$this->doctype('XHTML1_STRICT');?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->includeCode('header')?>
        <!--[if (gte IE 6)&(lte IE 8)]>
        <script type="text/javascript" src="/assets/jquerySelectivizr/selectivizr.min.js"></script>
        <![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1" />
    </head>
    <body class="frontend">
        <div id="page">
            <div id="outerHeader">
                <div class="logo">
                    <?=$this->component($this->boxes['logo']);?>
                </div>
                <div id="header">
                    <div class="searchBox">
                        <?=$this->component($this->boxes['searchBox']);?>
                    </div>
                    <div class="mainMenu">
                        <?=$this->component($this->boxes['mainMenu']);?>
                    </div>
                    <div class="clear"></div>
                    <div class="subMenuHorizonal">
                        <?=$this->component($this->boxes['subMenuHorizontal']);?>
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
                            <?=$this->componentWithMaster($this->componentWithMaster);?>
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
