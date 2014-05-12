<div class="<?=$this->cssClass?> kwfFadeElements">
    <input type="hidden" name="fadeConfig" class="fadeConfig" value="<?= htmlspecialchars(Zend_Json::encode($this->fadeConfig)); ?>" />
    <? foreach ($this->listItems as $item) { ?>
        <div class="subComponents <?=$item['class'];?>">
            <?=$this->component($item['data']);?>
        </div>
    <? } ?>
</div>
