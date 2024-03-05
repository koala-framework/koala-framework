<html>
    <head>
        <title>405 <?= $this->data->trlKwf('Method not allowed'); ?></title>
    </head>
    <body>
        <h1><?= $this->data->trlKwf('Method not allowed'); ?></h1>
        <p><?= $this->data->trlKwf('The {0} method is not allowed for the requested URL.', $this->method); ?></p>
    </body>
</html>
