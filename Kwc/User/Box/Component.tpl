<div class="<?=$this->cssClass?>">
    <? if ($this->placeholder['loginHeadline']) { ?>
        <h2><?=$this->placeholder['loginHeadline']?></h2>
    <? } ?>
    <?=$this->component($this->login)?>
    <ul>
        <li><?=$this->componentLink($this->register, trlVps('Register'))?><?=$this->linkPostfix?></li>
        <? if ($this->lostPassword) { ?>
        <li><?=$this->componentLink($this->lostPassword, trlVps('Lost password'))?><?=$this->linkPostfix?></li>
        <? } ?>
    </ul>
</div>
