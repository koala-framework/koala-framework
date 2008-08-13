<span class="<?=$this->cssClass?>">
<? for ($i = 0; $i < $this->rating; $i++) { ?>
    <?=$this->image($this->componentFile($this->data, 'rating.png'))?>
<? } ?>
</span>