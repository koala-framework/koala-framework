<html>
    <head>
        <title>500 Internal Server Error</title>
    </head>
    <body>
        <h1>Error</h1>

        <?php if ($this->debug) { ?>
            Errortype:<br />
            <pre><?= Kwf_Util_HtmlSpecialChars::filter($this->type) ?></pre>

            <br />

            Message:<br />
            <pre><?= Kwf_Util_HtmlSpecialChars::filter($this->exception) ?></pre>
            <?php if(isset($this->query)) { ?>
            <p>Last DB-Query:</p>
            <pre><?= Kwf_Util_HtmlSpecialChars::filter($this->query) ?></pre>
            <?php } ?>
        <?php } else { ?>
            <p>An Error ocurred. Please try again later.</p>
            <p>ErrorId: {logId}</p>
        <?php } ?>
        <?php if ($this->debug || isset($_COOKIE['unitTest'])) { ?>
        <?php try {
            echo '<p id="exception" style="display:none">'.Kwf_Util_HtmlSpecialChars::filter(base64_encode(serialize($this->exception))).'</p>';
        } catch (Exception $e) {} ?>
        <?php } ?>
    </body>
</html>
