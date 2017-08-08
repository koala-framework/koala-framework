<html>
    <head>
        <title>500 Internal Server Error</title>
    </head>
    <body>
        <h1>Error</h1>

        <?php if ($this->debug) { ?>
            Errortype:<br />
            <pre><?= htmlspecialchars($this->type) ?></pre>

            <br />

            Message:<br />
            <pre><?= htmlspecialchars($this->exception) ?></pre>
            <?php if(isset($this->query)) { ?>
            <p>Last DB-Query:</p>
            <pre><?= htmlspecialchars($this->query) ?></pre>
            <?php } ?>
        <?php } else { ?>
            <p>An Error ocurred. Please try again later.</p>
            <p>ErrorId: {logId}</p>
        <?php } ?>
        <?php if ($this->debug || isset($_COOKIE['unitTest'])) { ?>
        <?php try {
            echo '<p id="exception" style="display:none">'.htmlspecialchars(base64_encode(serialize($this->exception))).'</p>';
        } catch (Exception $e) {} ?>
        <?php } ?>
    </body>
</html>
