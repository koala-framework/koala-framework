<!-- metaTags -->
<?php foreach($this->metaTags as $name=>$content) { ?>
    <?php $attribute = substr($name, 0, 3) == 'og:' ? 'property' : 'name'; ?>
    <meta <?=$attribute?>="<?=htmlspecialchars($name)?>" content="<?=htmlspecialchars($content)?>" />
<?php } ?>
<link href="<?=$this->canonicalUrl?>" rel="canonical" />
<?php foreach($this->keys as $k) { ?>
    <?=$this->component($this->$k)?>
<?php } ?>
<!-- /metaTags -->
