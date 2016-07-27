<div class="item">
    <?php if ($this->item->categories) { ?>
        <div class="categories">
            <?php $nci = 0;
            foreach ($this->item->categories as $nc) {
                if (!$nc) continue;
                if ($nci++ >= 1) echo ' | ';
                echo $this->componentLink($nc);
            } ?>
            <div class="kwfUp-clear"></div>
        </div>
    <?php } ?>
    <div class="previewImage">
        <?=$this->componentLink($this->item, $this->component($this->item->getChildComponent('-previewImage')))?>
    </div>
    <div class="previewCenter">
        <div class="date"><?=$this->date($this->item->date)?>:</div>
        <h3><?=$this->componentLink($this->item, $this->item->title)?></h3>
        <div class="kwfUp-clear"></div>
        <div class="teaser">
            <?=$this->item->teaser?>
            <?=$this->componentLink($this->item, 'weiterlesen')?>
        </div>
    </div>
    <div class="kwfUp-clear"></div>
</div>
