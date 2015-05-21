<? if ($this->domain && $this->id) { ?>
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];

  <? if ($this->disableCookies) { ?>
  _paq.push(['disableCookies']);
  <? } else { ?>
  if (!Kwf.Statistics.isUserOptIn()) { _paq.push(['disableCookies']); }
  <? } ?>

  <? if ($this->customTrackingDomain) { ?>
  _paq.push(['setCookieDomain', '*.<?=$this->customTrackingDomain?>']);
  _paq.push(['setDomains', '*.<?=$this->customTrackingDomain?>']);
  _paq.push(['setDocumentTitle', document.domain + "/" + document.title]);
  <? } ?>

  <? foreach ($this->customVariables as $cv) { ?>
  _paq.push(<?=json_encode(array("setCustomVariable", $cv['index'], $cv['name'], $cv['value'], $cv['scope']))?>);
  <? } ?>

  <? foreach ($this->additionalConfiguration as $key => $val) { ?>
  _paq.push(["<?=$key?>", "<?=$val?>"]);
  <? } ?>

  _paq.push(["trackPageView"]);
  <? if ($this->enableLinkTracking) { ?>
  _paq.push(["enableLinkTracking"]);
  <? } ?>

  if (!location.search.match(/[\?&]kwcPreview/)) {
      (function() {
        var u=(("https:" == document.location.protocol) ? "https" : "http") + "://<?=$this->domain?>/";
        _paq.push(["setTrackerUrl", u+"piwik.php"]);
        _paq.push(["setSiteId", "<?=$this->id?>"]);
        var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
        g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
      })();
  }
</script>
<noscript>
    <img src="https://<?=$this->domain?>/piwik.php?idsite=<?=$this->id?>&amp;rec=1" style="border:0" alt="" />
</noscript>
<!-- End Piwik Code -->
<? } ?>