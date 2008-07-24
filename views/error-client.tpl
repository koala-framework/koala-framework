<html>
    <head>
        <title><?= trlVps('Error'); ?></title>
    </head>
    <body>
        <h1><?= trlVps('Error') ?></h1>
        <p><?= $this->exception->getMessage() ?></p>
    </body>
</html>
