<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <script type="text/javascript">
            //disable also for browsers that would support it
            window.history.pushState = false;
        </script>
        <?=$this->assets('Kwf_Js_HistoryStateNoHtml5:Test')?>
    </head>
    <body>
        <div id="testBtn1" style="height:20px;background-color:red;">sub</div>
        <div id="testBtn2" style="height:20px;background-color:blue;">index</div>

        <div>
            <h3>Result:</h3>
            <div id="result"><?=$this->result?></div>
        </div>
    </body>
</html>
