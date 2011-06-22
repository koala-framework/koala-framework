<div class="<?=$this->cssClass?>">
    <div class="account">
        <ul>
            <li class="mail">
                <?=$this->authedUser->email?>
            </li>
            <? if ($this->myProfile) { ?>
            <li class="profile">
                <?=$this->componentLink($this->myProfile, trlVps('My Profile'))?>
            </li>
            <? } ?>
            <? foreach ($this->links as $l) { ?>
                <li><?=$this->componentLink($l)?></li>
            <? } ?>
            <li class="logout"><a href="?logout"><?=trlVps('Logout')?></a></li>
        </ul>
        <div class="clear"></div>
    </div>
</div>