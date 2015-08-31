<!-- metaTags -->
<? foreach($this->metaTags as $name=>$content) { ?>
    <? $attribute = substr($name, 0, 3) == 'og:' ? 'property' : 'name'; ?>
    <meta <?=$attribute?>="<?=htmlspecialchars($name)?>" content="<?=htmlspecialchars($content)?>" />
<? } ?>
<? foreach($this->keys as $k) { ?>
    <?=$this->component($this->$k)?>
<? } ?>
<!-- /metaTags -->
