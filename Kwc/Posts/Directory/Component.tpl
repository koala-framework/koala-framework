<div class="<?=$this->cssClass?>">
    <div class="newEntry">
        <?if ($this->write) {?>
        <?=$this->componentLink($this->write, $this->placeholder['writeText'])?>
        <?}?>
        <?if ($this->quickwrite) {?>
        <?=$this->component($this->quickwrite)?>
        <?}?>
    </div>
    <?=$this->component($this->view)?>
</div>
