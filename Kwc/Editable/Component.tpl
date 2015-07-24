<div class="<?=$this->rootElementClass?>">
    <?php if ($this->hasContent($this->content)) {
        echo $this->component($this->content);
    } else {
        echo $this->data->trlKwf('No content specified. Please add in Admin -> Texts.');
    } ?>
</div>
