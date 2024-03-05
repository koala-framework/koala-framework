<html>
    <head>
        <title>403 <?= $this->data->trlKwf('Access Denied'); ?></title>
    </head>
    <body>
        <h1><?=Kwf_Util_HtmlSpecialChars::filter($this->message);?></h1>
        <p><?= $this->data->trlKwf('You are not allowed to enter this page.'); ?></p>
    </body>
</html>
