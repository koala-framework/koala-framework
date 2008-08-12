<div class="<?=$this->cssClass?>">
    <div class="postData">
        <? if ($this->user) { ?>
        <div class="avatar">
            <?= $this->componentLink($this->user, '<img src="/assets/images/logoLight.jpg" alt="Avatar" />') ?>
        </div>
        <div class="user">
            <?=trlVps('By')?>: <?= $this->componentLink($this->user) ?>
        </div>
        <?/*=trlVps('Member since')?>: <?=$this->date($this->user->created)*/?>
        <strong>#<?= $this->data->postNumber ?></strong>
        <i><?=$this->dateTime($this->data->row->create_time)?></i><br />
        Beitrag: 
        <?=$this->componentLink($this->report)?></a> | <?=$this->componentLink($this->quote)?></a>
        <div class="comment">
            <?=$this->content?>
        </div>
        <? } ?>
    </div>
    <div class="clear"></div>
    
    <? if ($this->signature) { ?>
        <p class="signature"><tt>--<br /><?=$this->signature?></tt></p>
    <?php } ?>
</div>