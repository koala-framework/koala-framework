<a name="<?=$this->item->componentId;?>"></a>
<div class="entry">
    <h2>
        <? if ($this->hasContent($this->item)) { ?>
            <?=$this->componentLink($this->item)?>
        <? } else { ?>
            <?=$this->item->row->title?>
        <? } ?>
    </h2>
    <div class="publishDate">
        <p><?=$this->date($this->item->publish_date);?></p>
    </div>
    <? if($this->hasContent($this->item->previewImage)) { ?>
        <div class="prevImage left">
            <?=$this->component($this->item->previewImage);?>
        </div>
    <? } ?>
    <? if($this->item->row->teaser) { ?>
	    <div class="teaser<? if($this->hasContent($this->item->previewImage)) { echo ' left'; } ?>">
	        <p>
	        	<?=nl2br($this->item->row->teaser);?>
				<? if($this->hasContent($this->item)) { ?>
				    <span class="readMoreLink">
				        <?=$this->componentLink($this->item, $this->item->trlVps('Read more').' &raquo;');?>
					</span>
				<? } ?>
			</p>
	    </div>
    <? } else if($this->hasContent($this->item)) { ?>
        <div class="readMoreLink">
            <?=$this->componentLink($this->item, $this->item->trlVps('Read more').' &raquo;');?>
        </div>
    <? } ?>
    <div class="clear"></div>
</div>
