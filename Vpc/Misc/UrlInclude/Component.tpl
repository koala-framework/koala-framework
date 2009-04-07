<div class="<?=$this->cssClass?>">
    <? if ($this->row->url) { ?>
        <? readfile($this->row->url) ?>
    <? } ?>
</div>
