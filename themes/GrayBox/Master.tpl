<?=$this->doctype('XHTML1_STRICT');?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->includeCode('header')?>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
    </head>
    <body class="frontend">
        <div id="page">
            <div id="outerHeader">
                <div id="header">
                    <div id="title">
                        <?=$this->component($this->boxes['headerTitle']);?>
                    </div>
                    <div id="searchBox">
                        <?=$this->component($this->boxes['searchBox']);?>
                    </div>
                </div>
            </div>
            <div id="outerContent">
                <div id="content">
                    <div id="mainMenu">
                        <?=$this->component($this->boxes['mainMenu']);?>
                    </div>
                    <div id="subMenu">
                        <?=$this->component($this->boxes['subMenu']);?>
                    </div>
                    <div id="innerContent">
                        <?=$this->componentWithMaster($this->componentWithMaster);?>
                    </div>
                </div>
            </div>
            <div id="outerFooter">
                <div id="footer">
                    <?=$this->component($this->boxes['bottomMenu']);?>
                </div>
            </div>
        </div>
        <?=$this->includeCode('footer')?>
    </body>
</html>
