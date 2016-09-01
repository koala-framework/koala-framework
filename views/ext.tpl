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
            document.write('<?=$this->image('/assets/ext2/resources/images/default/shared/large-loading.gif')?>');
            document.write('<?= addslashes($this->applicationName) ?><br /><span id="loading-msg"><?= trlKwf('Loading...') ?></span></div>');
        document.write('</div>');
    </script>

    <?php echo $this->debugData() ?>
    <?php echo $this->assets($this->ext['assetsPackage']) ?>

    <script type="text/javascript">
    (function() {
        <?php if ($this->uniquePrefix) { ?>
        var Kwf = <?=$this->uniquePrefix?>.Kwf;
        var Ext2 = <?=$this->uniquePrefix?>.Ext2;
        <?php } ?>
        <?php if (isset($this->ext['user'])) { ?>
        Kwf.user = '<?= $this->ext['user'] ?>';
        <?php } ?>
        Kwf.userRole = '<?= $this->ext['userRole'] ?>';
        <?php if (isset($this->sessionToken)) { ?>
        Kwf.sessionToken = '<?= $this->sessionToken ?>';
        <?php } ?>
        Kwf.main = function() {
            <?php if ($this->ext['class']) { ?>
            var panel = new <?= $this->ext['class'] ?>(<?= Zend_Json::encode($this->ext['config']) ?>);
            <?php } else { ?>
            var panel = <?= Zend_Json::encode($this->ext['config']) ?>;
            <?php } ?>
            Kwf.currentViewport = new <?= $this->ext['viewport'] ?>({
                items: [panel]
            });
            if((!Kwf.Connection || Kwf.Connection.masks == 0)  && Ext2.get('loading')) {
                Ext2.get('loading').fadeOut({remove: true});
            }
            Kwf.activateKeepAlive();
        };
    })();
    </script>
