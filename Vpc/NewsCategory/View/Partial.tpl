<a name="<?= $this->item->componentId; ?>"></a>
<div class="entry">
    <div class="text">
        <h1><?=$this->componentLink($this->item)?></h1>
        <p>
            <span class="publishDate"><?=$this->date($this->item->publish_date)?>
                <?
                if ($this->item->categories) { ?>
                    | <?= trlp('Kategorie', 'Kategorien', count($this->item->categories)); ?>
                    <? $nci = 0;
                    foreach ($this->item->categories as $nc) {
                        if ($nci++ >= 1) echo ', ';
                        echo $this->componentLink($nc);
                    } ?>
                <? } ?>
            </span>
            <div class="clear"></div>
        </p>
    </div>
    <div class="clear"></div>
    <? if (isset($this->item->previewImage)) { ?>
    <div class="image left">
        <?=$this->componentLink($this->item, $this->component($this->item->previewImage))?>
    </div>
    <? } ?>
    <div class="teaser">
        <p><?=$this->item->row->teaser?></p>
        <?=$this->componentLink($this->item,trl('weiterlesen').' »');?>
    </div>
    <div class="clear"></div>
</div>
