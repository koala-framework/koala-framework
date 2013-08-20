<? if ($this->domain && $this->id) { ?>
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  <? if ($this->disableCookies) { ?>
  _paq.push(['disableCookies']);
  <? } else { ?>
  if (!Kwf.Statistics.isUserOptIn()) { _paq.push(['disableCookies']); }
  <? } ?>
  <? foreach ($this->customVariables as $cv) { ?>
  _paq.push(["setCustomVariable", <?=$cv['index']?>, "<?=$cv['name']?>", "<?=$cv['value']?>", "<?=$cv['scope']?>"]);
  <? } ?>
  _paq.push(["trackPageView"]);
  <? if ($this->enableLinkTracking) { ?>
  _paq.push(["enableLinkTracking"]);
  <? } ?>

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://<?=$this->domain?>/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "<?=$this->id?>"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript>
    <img src="https://<?=$this->domain?>/piwik.php?idsite=<?=$this->id?>&amp;rec=1" style="border:0" alt="" />
</noscript>
<!-- End Piwik Code -->
<? } ?>