<div class="<?=$this->cssClass?>">
    <span class="beforeButton"></span>
    <span class="button">
        <div class="newEntry">
            <?=$this->componentLink($this->write, $this->placeholder['writeText'])?>
        </div>
    </span>
    <span class="afterButton"></span>
    <?=$this->component($this->view)?>
</div>