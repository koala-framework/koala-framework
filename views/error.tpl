<html>
    <head>
    </head>
    <body>
        <h1><?= trlVps('Error') ?></h1>

        <?php if ($this->debug) { ?>
            <?= trlVps('Errortype') ?>:<br />
            <pre><?= $this->type ?></pre>

            <br />

            <?= trlVps('Message') ?>:<br />
            <pre><?= $this->exception ?></pre>
            <?php if(isset($this->query)) { ?>
            <p><?= trlVps('Last DB-Query') ?>:</p>
            <pre><?= $this->query ?></pre>
            <?php } ?>
        <?php } else { ?>
            <?= trlVps('An Error ocurred. Please try again later.') ?>
        <?php } ?>

    </body>
</html>