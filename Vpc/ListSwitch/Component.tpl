<div class="vpsListSwitch <?=$this->cssClass?>">
    <div class="listSwitchLargeWrapper">
        <div class="listSwitchLargeContent">
            <? foreach ($this->children as $child) {
                // diese ausgabe ist nur um flackern zu unterbinden. könnte
                // auch entfernt werden, da das bild sowieso vom javascript
                // nochmal gesetzt wird.
            ?>
                <?= $this->component($child->getChildComponent('-large'));
                break; ?>
            <? } ?>
        </div>
        <a href="#" class="listSwitchPrevious"><?= trlVps('previous'); ?></a>
        <a href="#" class="listSwitchNext"><?= trlVps('next'); ?></a>
    </div>

    <div class="listSwitchPreviewWrapper">
        <? $i = 0; ?>
        <? foreach ($this->children as $child) { ?>
            <?
                $class = '';
                if ($i == 0) $class .= 'vpcFirst ';
                if ($i == count($this->children)-1) $class .= 'vpcLast ';
                $class = trim($class);
                $i++;
            ?>
            <div class="listSwitchItem <?= $class; ?>">
                <a href="#" class="previewLink"><?=$this->component($child);?></a>
                <div class="largeContent"><?= $this->component($child->getChildComponent('-large')); ?></div>
            </div>
        <? } ?>
        <div class="clear"></div>
    </div>
</div>
