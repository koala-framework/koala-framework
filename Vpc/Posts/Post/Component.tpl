<div class="<?=$this->cssClass?>">
    <div class="postData">
        <? if ($this->user) { ?>
        <div class="avatar">
            <?= $this->componentLink($this->user, '<img src="/assets/images/logoLight.jpg" alt="Avatar" />') ?>
        </div>
        <?=trlVps('By')?>: <?= $this->componentLink($this->user) ?>
        <?=trlVps('Member since')?>: <?= $this->date($this->user->row->created) ?>
        <? } ?>
        <strong>#<?= $this->data->postNumber ?></strong>
        <i><?=$this->dateTime($this->data->row->create_time)?></i>
        <? if ($this->edit) { ?>
            <br /><?=$this->componentLink($this->edit)?>
        <? } ?>
        <? if ($this->delete) { ?>
            <br /><?=$this->componentLink($this->delete)?>
        <? } ?>

        <?=$this->componentLink($this->report)?>
        <?=$this->componentLink($this->quote)?>
    </div>
    <div class="clear"></div>
    <div class="content">
        <?=$this->content?>
    </div>
    <? if ($this->signature) { ?>
        <p class="signature"><tt>--<br /><?=$this->signature?></tt></p>
    <?php } ?>
</div>