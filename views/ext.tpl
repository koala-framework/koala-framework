    <style type="text/css">
    #loading {
        position:absolute;
        left:45%;
        top:40%;
        padding:2px;
        z-index:20001;
        height:auto;
        border:1px solid #ccc;
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

    <?= $this->assets($this->ext['assetsType']) ?>
    <?= $this->debugData() ?>

    <script type="text/javascript">
        Vps.userRole = '<?= $this->ext['userRole'] ?>';
        Vps.main = function() {
            var panel = new <?= $this->ext['class'] ?>(<?= $this->ext['config'] ?>);
            if (!panel.region) panel.region = 'center';
            panel.id = 'mainPanel';
            Vps.currentViewport = new <?= $this->ext['viewport'] ?>({
                items: [panel]
            });
            if(Vps.Connection.masks == 0  && Ext.get('loading')) {
                Ext.get('loading').fadeOut({remove: true});
            }
        };
        Ext.onReady(function() {
            Vps.callWithErrorHandler(Vps.main);
        });
    </script>
