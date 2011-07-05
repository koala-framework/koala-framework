<? if ($this->child) { ?>
    <div id="vpcEnlargeLightbox-<?=$this->data->componentId?>" style="display: none">
    <?=$this->component($this->child)?>
    </div>
    <a class="vpcEnlargeLightbox" href="<?=$this->url?>" rel="size_<?=$this->width?>_<?=$this->height?> id_<?=$this->data->componentId?>">
<? } ?>