<div class="<?=$this->cssClass?>">
<? if ($this->authedUser) { ?>
    <div class="account">
        <h2><?=trlVps('My account')?>:</h2>
        <ul>
            <? if ($this->myProfile) { ?>
            <li><?=$this->componentLink($this->myProfile, trlVps('My Profile'))?></li>
            <? } ?>
            <? foreach ($this->links as $l) { ?>
                <li><?=$this->componentLink($l)?></li>
            <? } ?>
            <li class="logout"><a href="?logout"><?=trlVps('Logout')?></a></li>
        </ul>
    </div>
<? } else { ?>
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
<? } ?>
</div>
