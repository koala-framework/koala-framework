<html>
    <head>
        <title>404 <?= trlKwf('Not Found'); ?></title>
    </head>
    <body>
        <h1><?= trlKwf('Not Found'); ?></h1>
        <p><?= trlKwf('The requested URL "{0}" was not found on this server.', $this->requestUri); ?></p>
    </body>
</html>