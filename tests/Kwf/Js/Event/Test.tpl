<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head><?=$this->assets('Vps_Js_Event:Test')?></head>
    <body>
        <div style="margin: 20px; padding: 10px;">
            <div id="eventTest" style="width: 200px; height: 80px; background-color: #0ad;">
                <a href="#" id="eventTestA">
                    <strong id="eventTestStrong">Lorem Ipsum</strong><br />
                    <span id="eventTestSpan">Dolor sit</span>
                </a>
            </div>
        </div>

        <div>
            <h3>Result:</h3>
            <div id="result"></div>
        </div>
    </body>
</html>
