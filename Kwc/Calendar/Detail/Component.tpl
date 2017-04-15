<div class="<?=$this->cssClass?>">
    <h2>
        <?=$this->row->title;?>
    </h2>
    <h3>Start:</h3>
    <p>
        <?=$this->row->from;?>
    </p>
    <?if($this->row->to) {?>
    <h3>Ende:</h3>
    <p>
        <?=$this->row->to;?>
    </p>
    <?}?>
    <?if($this->row->description) {?>
    <div class="placeholder"></div>
    <h3>Beschreibung:</h3>
    <p>
        <?=$this->row->description;?>
    </p>
    <?}?>
</div>