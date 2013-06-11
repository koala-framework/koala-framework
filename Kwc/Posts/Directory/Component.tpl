<div class="<?=$this->cssClass?>">
    <? if ($this->write) { ?>
        <div class="write">
            <?=$this->componentLink($this->write, $this->placeholder['writeText'])?>
        </div>
    <? } ?>

    <div class="comments">
        <?=$this->component($this->view)?>
    </div>

    <? if ($this->quickwrite) { ?>
        <div class="quickwrite">
            <?=$this->component($this->quickwrite)?>
        </div>
    <? } ?>
</div>
