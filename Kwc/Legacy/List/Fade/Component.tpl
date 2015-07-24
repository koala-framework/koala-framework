<div class="<?=$this->rootElementClass?> kwfFadeElements">
    <? /* fadeSelector ist pflicht, alles andere optional */ ?>
    <input type="hidden" name="fadeSelector" class="fadeSelector" value="<?= $this->selector; ?>" />
    <input type="hidden" name="textSelector" class="textSelector" value="<?= $this->textSelector; ?>" />
    <input type="hidden" name="fadeConfig" class="fadeConfig" value="<?= htmlspecialchars(Zend_Json::encode($this->fadeConfig)); ?>" />
    <input type="hidden" name="fadeClass" class="fadeClass" value="<?= $this->fadeClass; ?>" />

    <? $i = 0; ?>
    <? foreach ($this->children as $child) { ?>
        <?
            $class = '';
            if ($i == 0) $class .= 'kwcFirst ';
            if ($i == count($this->children)-1) $class .= 'kwcLast ';
            if ($i % 2 == 0) {
                $class .= ' kwcEven';
            } else {
                $class .= ' kwcOdd';
            }
            $class = trim($class);
            $i++;
        ?>
        <div class="subComponents <?= $class; ?>">
            <?=$this->component($child);?>
        </div>
    <? } ?>
</div>
