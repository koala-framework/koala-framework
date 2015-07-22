<div class="<?=$this->rootElementClass?>">
    <? if ($this->placeholder['loginHeadline']) { ?>
        <h2><?=$this->placeholder['loginHeadline']?></h2>
    <? } ?>
    <?=$this->component($this->login)?>
    <ul>
        <? if ($this->register) { ?>
        <li><?=$this->componentLink($this->register, $this->data->trlKwf('Register'))?><?=$this->linkPostfix?></li>
        <? } ?>
        <? if ($this->lostPassword) { ?>
        <li><?=$this->componentLink($this->lostPassword, $this->data->trlKwf('Lost password'))?><?=$this->linkPostfix?></li>
        <? } ?>
    </ul>
</div>
