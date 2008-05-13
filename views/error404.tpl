<html>
    <head>
    </head>
    <body>
        <h1><?= trlVps('Page not found'); ?></h1>

        <?= trlVps('Errortype'); ?>:<br />
        <pre><?= $this->type ?></pre>

        <br />

        <?= trlVps('Message'); ?>:<br />
        <pre><?= $this->exception ?></pre>

    </body>
</html>