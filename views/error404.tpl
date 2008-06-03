<html>
    <head>
        <title>404 <?= trlVps('Not Found'); ?></title>
    </head>
    <body>
        <h1><?= trlVps('Not Found'); ?></h1>
        <p><?= trlVps('The requested URL "{0}" was not found on this server.', $this->requestUri); ?></p>
    </body>
</html>