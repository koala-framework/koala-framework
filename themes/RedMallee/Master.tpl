<?=$this->doctype('XHTML1_STRICT');?>
<!--[if lt IE 9 ]><html xmlns="http://www.w3.org/1999/xhtml" class="no-mediaqueries"> <![endif]-->
<!-- [if !(lt IE 9)]> -->
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- <![endif] -->
    <head>
        <?=$this->includeCode('header')?>
        <!--[if (gte IE 6)&(lte IE 8)]>
        <script type="text/javascript" src="/assets/jquerySelectivizr/selectivizr.min.js"></script>
        <![endif]-->
        <link type="text/css" rel="stylesheet" href="http://fast.fonts.net/cssapi/245016a6-66c1-4998-9b9c-874900361e1f.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    </head>
    <body class="<?=$this->cssClass?>">
        <div id="outerBg">
            <?=$this->component($this->boxes['background']);?>
        </div>
        <div id="mask"></div>
        <div id="page">
            <div id="outerHeader">
                <div id="header">
                    <div class="logo">
                        <a href="/">
                            <?=$this->component($this->boxes['logo']);?>
                        </a>
                    </div>
                    <div class="rightBox">
                        <div class="searchBox">
                            <?=$this->component($this->boxes['searchBox']);?>
                        </div>
                        <div class="topMenu">
                            <?=$this->component($this->boxes['topMenu']);?>
                        </div>
                        <div class="mainMenu">
                            <?=$this->component($this->boxes['mainMenu']);?>
                        </div>
                        <div class="verticalMenu">
                            <?=$this->component($this->boxes['verticalMenu']);?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                    <div class="subMenuHorizontal">
                        <?=$this->component($this->boxes['subMenuHorizontal']);?>
                    </div>
                    <div class="subSubMenuHorizontal">
                        <?=$this->component($this->boxes['subSubMenuHorizontal']);?>
                    </div>
                </div>
            </div>
            <div id="outerContent">
                <div class="stage">
                    <?=$this->component($this->boxes['listFade']);?>
                </div>
                <div id="content">
                    <div class="breadcrumbs">
                        <?=$this->component($this->boxes['breadcrumbs']);?>
                    </div>
                    <div id="innerContent">
                        <div class="leftColumn">
                            <?=$this->component($this->boxes['subMenu']);?>
                        </div>
                        <div class="centerColumn">
                            <?=$this->componentWithMaster($this->componentWithMaster);?>
                        </div>
                        <!--
                        <div class="rightColumn">
                            <?=$this->component($this->boxes['rightBox']);?>
                        </div>
                        -->
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="outerFooter">
            <div id="footer">
                <div class="leftArea">
                    <?=$this->component($this->boxes['footerImage']);?>
                </div>
                <div class="bottomMenu">
                    <?=$this->component($this->boxes['bottomMenu']);?>
                </div>
            </div>
        </div>
        <?=$this->includeCode('footer')?>
    </body>
</html>
