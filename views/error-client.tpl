<html>
    <head>
        <title><?= trlKwf('Error'); ?></title>
    </head>
    <body>
        <h1><?= trlKwf('Error') ?></h1>
        <p><?= Kwf_Util_HtmlSpecialChars::filter($this->message) ?></p>
    </body>
</html>
