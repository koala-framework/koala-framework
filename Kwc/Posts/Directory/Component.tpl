<div class="<?=$this->cssClass?>">
    <span class="beforeButton"></span>
    <span class="button">
        <div class="newEntry">
            <?if ($this->write) {?>
            <?=$this->componentLink($this->write, $this->placeholder['writeText'])?>
            <?}?>
            <?if ($this->quickwrite) {?>
            <?=$this->component($this->quickwrite)?>
            <?}?>
        </div>
    </span>
    <span class="afterButton"></span>
    <?=$this->component($this->view)?>
</div>