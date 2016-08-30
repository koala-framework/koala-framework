<li class="products">
    <?php if ($this->item->row->getItem()->SmallImage) { ?>
    <img src="<?=$this->item->row->getItem()->SmallImage->Url->__toString()?>" width="50px" height="<?=$this->item->row->getItem()->SmallImage->Height?>" alt="" />
    <?php } ?>
    <h1><?=$this->componentLink($this->item);?></h1>
    <h2><?=$this->item->row->author?></h2>
    <p><?=$this->componentLink($this->item, $this->data->trlKwf('more information').' »');?></p>
    <div class="kwfUp-clear"></div>
</li>
