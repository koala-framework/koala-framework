<? if ($this->domain && $this->id) { ?>
<!-- Piwik -->
<script type="text/javascript">
    var pkBaseURL = (("https:" == document.location.protocol) ? "https://<?=$this->domain?>/" : "<?=$this->domain?>/");
    document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
    try {
        var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", <?=$this->id?>);
        <? foreach ($this->customVariables as $cv) { ?>
        piwikTracker.setCustomVariable(<?=$cv['index']?>, '<?=$cv['name']?>', '<?=$cv['value']?>', '<?=$cv['scope']?>');
        <? } ?>
        piwikTracker.trackPageView();
        piwikTracker.enableLinkTracking();
    } catch( err ) {}
</script>
<noscript><p><img src="http://<?=$this->domain?>/piwik.php?idsite=<?=$this->id?>" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->
<? } ?>