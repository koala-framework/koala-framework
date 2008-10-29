<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>404 <?= trlVps('Not Found'); ?></title>
    </head>
    <body>
        <h1><?= trlVps('Not Found'); ?></h1>
        <p><?= trlVps('The requested URL "{0}" was not found on this server.', $this->requestUri); ?></p>
        <p><a href="/">Klicken Sie bitte hier</a>, um zur Startseite zu gelangen.</p>
    </body>
</html>
