<div class="<?=$this->cssClass?>">
<? for ($i = 0; $i < $this->rating; $i++) { ?>
    <?=$this->image($this->componentFile($this->data, 'star.png'))?>
<? } ?>
</div>