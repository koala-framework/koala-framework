<div class="<?=$this->cssClass?>">
<? if ($this->authedUser) { ?>
    <div class="account">
        <h2><?=trlVps('My account')?>:</h2>
        <ul>
            <li><?=$this->componentLink($this->myProfile, trlVps('My Profile'))?></li>
            <? foreach ($this->links as $l) { ?>
                <li><?=$this->componentLink($l)?></li>
            <? } ?>
            <li class="logout"><a href="?logout">Logout</a></li>
        </ul>
    </div>
<? } else { ?>
    <h3>Login:</h3>
    <?=$this->component($this->login)?>
    <ul>
        <li><?=$this->componentLink($this->register, trlVps('Register'))?> &raquo;</li>
        <li><?=$this->componentLink($this->lostPassword, trlVps('Lost password'))?> &raquo;</li>
    </ul>
<? } ?>
</div>
