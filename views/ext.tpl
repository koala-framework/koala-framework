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

    <div id="<?=$this->uniquePrefix?>extconfig"
        <?php if (isset($this->ext['user'])) { ?>
            data-user="<?= Kwf_Util_HtmlSpecialChars::filter($this->ext['user']) ?>"
        <?php } ?>
        data-user-role="<?= Kwf_Util_HtmlSpecialChars::filter($this->ext['userRole']) ?>"
        <?php if ($this->ext['class']) { ?>
            data-ext-class="<?= Kwf_Util_HtmlSpecialChars::filter($this->ext['class']) ?>"
        <?php } ?>
        data-ext-config="<?= Kwf_Util_HtmlSpecialChars::filter(json_encode($this->ext['config'])) ?>"
        data-ext-viewport="<?= Kwf_Util_HtmlSpecialChars::filter($this->ext['viewport']) ?>"
    ></div>

    <script type="text/javascript">
    (function() {
        <?php /* TODO if ($this->uniquePrefix) { ?>
        var Kwf = <?=$this->uniquePrefix?>.Kwf;
        var Ext2 = <?=$this->uniquePrefix?>.Ext2;
        <?php }*/ ?>

        var configEl = document.getElementById('<?=$this->uniquePrefix?>extconfig');
        configEl.parentNode.removeChild(configEl);

        Kwf.userRole = configEl.getAttribute('data-user-role');
        Kwf.user = configEl.getAttribute('data-user');
        Kwf.main = function() {
            var extClass = configEl.getAttribute('data-ext-class');
            var extConfig = JSON.parse(configEl.getAttribute('data-ext-config'));
            var extViewport = configEl.getAttribute('data-ext-viewport');

            var panel;
            if (extClass) {
                panel = new (eval(extClass))(extConfig);
            } else {
                panel = extConfig;
            }
            Kwf.currentViewport = new (eval(extViewport))({
                items: [panel]
            });
            if((!Kwf.Connection || Kwf.Connection.masks == 0)  && Ext2.get('loading')) {
                Ext2.get('loading').fadeOut({remove: true});
            }
            Kwf.activateKeepAlive();
        };
    })();
    </script>
