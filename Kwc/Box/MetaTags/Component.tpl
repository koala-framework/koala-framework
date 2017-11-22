<!-- <?=$this->kwfUp?>metaTags -->
<?php foreach($this->metaTags as $name=>$content) { ?>
    <?php $attribute = substr($name, 0, 3) == 'og:' ? 'property' : 'name'; ?>
    <meta <?=$attribute?>="<?=Kwf_Util_HtmlSpecialChars::filter($name)?>" content="<?=Kwf_Util_HtmlSpecialChars::filter($content)?>" />
<?php } ?>
<link href="<?=$this->canonicalUrl?>" rel="canonical" />
<?php foreach($this->keys as $k) { ?>
    <?=$this->component($this->$k)?>
<?php } ?>
<!-- /<?=$this->kwfUp?>metaTags -->
