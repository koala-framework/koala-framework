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
            document.write('<?=$this->image('/assets/ext/resources/images/default/shared/large-loading.gif')?>');
            document.write('<?= $this->applicationName ?><br /><span id="loading-msg"><?= trlKwf('Loading...') ?></span></div>');
        document.write('</div>');
        var Kwf = {isApp: true};
    </script>

    <?= $this->debugData() ?>
    <?= $this->assets($this->assetsPackage) ?>

    <script type="text/javascript">
    <?php if ($this->user) { ?>
    Kwf.user = '<?= $this->user ?>';
    <?php } ?>
    Kwf.userRole = '<?= $this->userRole ?>';
    <? if (isset($this->sessionToken)) { ?>
    Kwf.sessionToken = '<?= $this->sessionToken ?>';
    <? } ?>
    Ext4.application({
        name: 'App',
        controllers: ['<?=$this->extController?>'],
        launch: function() {
            Ext4.get('loading').fadeOut({remove: true});
        }
    });
    </script>
