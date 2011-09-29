<? if ($this->imageUrl) { ?>
    <a class="vpcEnlargeTag" href="<?=$this->imagePage->url?>" rel="<?=htmlspecialchars($this->imagePage->rel)?>">
    <input type="hidden" class="options" value="<?=htmlspecialchars(Zend_Json::encode($this->options))?>" />
<? } ?>