<? if ($this->code) { ?>
<!-- Google Analytics -->
<script type="text/javascript">
    <? if ($this->ignoreCode) { echo "/*"; } ?>
    if (!location.search.match(/[\?&]kwcPreview/)) {
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new
        Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '<?=$this->code?>', 'auto');
        ga('set', 'anonymizeIp', true);
        ga('send', 'pageview');
    }
    <? if ($this->ignoreCode) { echo "*/"; } ?>
</script>
<!-- End Google Analytics -->
<? } ?>
