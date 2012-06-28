<div class="<?=$this->cssClass?>">
    <? if ($this->placeholder['loginHeadline']) { ?>
        <h2><?=$this->placeholder['loginHeadline']?></h2>
    <? } ?>
    <?=$this->component($this->login)?>
    <ul>
        <? if ($this->register) { ?>
        <li><?=$this->componentLink($this->register, trlKwf('Register'))?><?=$this->linkPostfix?></li>
        <? } ?>
        <? if ($this->lostPassword) { ?>
        <li><?=$this->componentLink($this->lostPassword, trlKwf('Lost password'))?><?=$this->linkPostfix?></li>
        <? } ?>
    </ul>
</div>
