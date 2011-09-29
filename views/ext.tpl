    <meta name="viewport" content="user-scalable=no" />
    <style type="text/css">
    #loading {
        position:absolute;
        left:45%;
        top:40%;
        padding:2px;
        z-index:20001;
        height:auto;
        border:1px solid #ccc;
        min-width: 100px;
    }
    #loading .loading-indicator{
        background:#f6f6f6;
        color:#444;
        font:bold 13px tahoma,arial,helvetica;
        padding:10px;
        margin:0;
        height:auto;
    }
    #loading .loading-indicator img {
        margin-right:8px;
        float:left;
        vertical-align:top;
    }
    #loading-msg {
        font: normal 10px arial,tahoma,sans-serif;
    }
    </style>

    <script type="text/javascript">
        document.write('<div id="loading">');
          document.write('<div class="loading-indicator">');
            document.write('<img src="/assets/ext/resources/images/default/shared/large-loading.gif" width="32" height="32"/>');
            document.write('<?= $this->applicationName ?><br /><span id="loading-msg"><?= trlVps('Loading...') ?></span></div>');
        document.write('</div>');
        var Vps = {isApp: true};
    </script>

    <?= $this->debugData() ?>
    <?= $this->assets($this->ext['assetsType']) ?>

    <script type="text/javascript">
        Vps.userRole = '<?= $this->ext['userRole'] ?>';
        Vps.main = function() {
            <? if ($this->ext['class']) { ?>
            var panel = new <?= $this->ext['class'] ?>(<?= Zend_Json::encode($this->ext['config']) ?>);
            <? } else { ?>
            var panel = <?= Zend_Json::encode($this->ext['config']) ?>;
            <? } ?>
            Vps.currentViewport = new <?= $this->ext['viewport'] ?>({
                items: [panel]
            });
            if(Vps.Connection.masks == 0  && Ext.get('loading')) {
                Ext.get('loading').fadeOut({remove: true});
            }
        };
        Ext.onReady(function() {
            Vps.main();
        });
    </script>
