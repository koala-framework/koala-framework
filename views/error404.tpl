<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>404 - <?=$this->data->trlKwf('File not found');?> - <?=Kwf_Config::getValue('application.name')?></title>
        <?= $this->assets(Kwf_Assets_Package_Default::getInstance('Frontend')) ?>
        <?= $this->debugData() ?>
        <link rel="shortcut icon" href="/assets/web/images/favicon.ico" />
    </head>
    <body class="frontend error404">
        <div id="page">
            <div id="outerContent">
                <div id="content">
                    <div class="kwfup-webStandard" id="innerContent">
                        <p><strong><?=$this->data->trlKwf('Errormessage');?></strong></p>
                        <h2>404 - <?=$this->data->trlKwf('File not found');?></h2>
                        <p><?=$this->data->trlKwf('The requested URL "{0}" was not found on this server.', $this->requestUri);?></p>
                        <ul>
                            <li>
                                <?=$this->data->trlKwf('If you typed the address, make sure the spelling is correct');?>.<br/>
                            </li>
                            <li>
                                <?=$this->data->trlKwf('Check the page you are coming from');?>.<br/><br/>
                            </li>
                        </ul>
                        <p><strong><a href="/">&laquo; <?=$this->data->trlKwf('Go back to main page');?></a></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
