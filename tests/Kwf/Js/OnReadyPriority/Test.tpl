<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head><?=$this->assets('Kwf_Js_OnReadyPriority:Test')?></head>
    <body>
        <div style="margin: 20px; padding: 10px;">
            <div class="onReadyPriorityTest" style="width: 200px; height: 80px; background-color: #0ad;">
                <a href="#" class="onReadyPriorityTestA">
                    <strong class="onReadyPriorityTestStrong">Lorem Ipsum</strong><br />
                    <span class="onReadyPriorityTestSpan">Dolor sit</span>
                </a>
            </div>
        </div>

        <div>
            <h3>Result:</h3>
            <div id="result"></div>
        </div>
    </body>
</html>
