<div class="<?=$this->rootElementClass?>">
    <div class="account">
        <ul>
            <li class="mail">
                <?=htmlspecialchars($this->authedUser->email)?>
            </li>
            <?php if ($this->myProfile) { ?>
            <li class="profile">
                <?=$this->componentLink($this->myProfile, $this->data->trlKwf('My Profile'))?>
            </li>
            <?php } ?>
            <?php foreach ($this->links as $l) { ?>
                <li><?=$this->componentLink($l)?></li>
            <?php } ?>
            <li class="logout"><a href="<?=htmlspecialchars($this->logoutLink)?>"><?=$this->data->trlKwf('Logout')?></a></li>
        </ul>
        <div class="kwfUp-clear"></div>
    </div>
</div>
