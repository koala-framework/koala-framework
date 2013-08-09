<div class="<?=$this->cssClass?>">
    <?=$this->data->trlKwf('This website uses cookies to help us give you the best experience when you visit our website.')?>
    <? if ($this->optComponent) {
        echo $this->componentLink($this->optComponent, $this->data->trlKwf('More information about the use of cookies'), 'info');
    }?>
    <?=$this->componentLink($this->data, $this->data->trlKwf('Accept and continue'), array('cssClass' => 'accept', 'get' => array('accept' => true)));?>
</div>