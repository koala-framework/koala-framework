<? if ($this->video) { ?>
    <div id="vpcEnlargeLightbox-<?=$this->data->componentId?>" style="display: none">
    <?=$this->component($this->video)?>
    </div>
    <a class="vpcEnlargeLightbox" href="<?=$this->videoUrl?>" rel="size_<?=$this->width?>_<?=$this->height?> id_<?=$this->data->componentId?>">
<? } ?>