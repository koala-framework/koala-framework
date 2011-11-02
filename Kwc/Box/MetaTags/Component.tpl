<? if (Kwf_Config::getValue('application.kwf.name') == 'Koala Framework') { ?>

<!--
    This website is powered by Koala Web Framework CMS Version <?=Kwf_Config::getValue('application.kwf.version')?>.
    Koala Framework is a free open source Content Management Framework licensed under BSD.
    http://www.koala-framework.org
-->
<? } ?>
<? foreach($this->metaTags as $name=>$content) { ?>
    <meta name="<?=htmlspecialchars($name)?>" content="<?=htmlspecialchars($content)?>" />
<? } ?>
