<? if ($this->code) { ?>
    <? if ($this->ignoreCode) { ?>
        <!--
        Ignore Code, because of config setting statistics.ignore or statistics.analytics.ignore
    <? } ?>
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '<?=$this->code?>']);
      _gaq.push(['_trackPageview']);

      if (!location.search.match(/[\?&]kwcPreview/)) {
          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
      }

    </script>
    <? if ($this->ignoreCode) { ?>
        -->
    <? } ?>
<? } ?>
