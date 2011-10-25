<? if ($this->child) { ?>
    <div id="kwcEnlargeLightbox-<?=$this->data->componentId?>" style="display: none">
    <?=$this->component($this->child)?>
    </div>
    <a class="kwcEnlargeLightbox" href="<?=$this->url?>" rel="size_<?=$this->width?>_<?=$this->height?> id_<?=$this->data->componentId?>">
<? } ?>