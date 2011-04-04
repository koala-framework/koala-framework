<? if ($this->imageUrl) { ?>
    <a class="vpcEnlargeTag" href="<?=$this->imageUrl?>" rel="enlarge_<?=$this->width?>_<?=$this->height?>">
    <input type="hidden" class="options" value="<?=htmlspecialchars(Zend_Json::encode($this->options))?>" />
<? } ?>