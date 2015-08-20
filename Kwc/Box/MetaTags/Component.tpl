<!-- metaTags -->
<? foreach($this->metaTags as $name=>$content) { ?>
    <meta name="<?=htmlspecialchars($name)?>" content="<?=htmlspecialchars($content)?>" />
<? } ?>
<? foreach($this->keys as $k) { ?>
    <?=$this->component($this->$k)?>
<? } ?>
<!-- /metaTags -->
