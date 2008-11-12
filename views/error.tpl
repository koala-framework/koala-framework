<html>
    <head>
        <title>500 <?= trlVps('Internal Server Error'); ?></title>
    </head>
    <body>
        <h1><?= trlVps('Error') ?></h1>

        <?php if ($this->debug) { ?>
            Errortype:<br />
            <pre><?= $this->type ?></pre>

            <br />

            Message:<br />
            <pre><?= $this->exception ?></pre>
            <?php if(isset($this->query)) { ?>
            <p>Last DB-Query:</p>
            <pre><?= $this->query ?></pre>
            <?php } ?>
            <p id="exception" style="display:none"><?=base64_encode(serialize($this->exception))?></p>
        <?php } else { ?>
            <?= trlVps('An Error ocurred. Please try again later.') ?>
        <?php } ?>

    </body>
</html>
