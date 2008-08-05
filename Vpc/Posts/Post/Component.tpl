<div class="<?=$this->cssClass?>">
    <div class="postData">
        <? if ($this->user) { ?>
        <?=trlVps('By')?>: <?=$this->user?>
        <?=trlVps('Member since')?>: <?=$this->date($this->user->created)?>
        <? } ?>
        <strong>#TODO:</strong>
        <i><?=$this->dateTime($this->data->row->create_time)?></i>
        <? if ($this->edit) { ?>
            <br /><?=$this->componentLink($this->edit, trlVps('Change Post'))?></a>
        <? } ?>
        <? if ($this->report) { ?>
            <br /><?=$this->componentLink($this->report, trlVps('Report Post'))?></a>
        <? } ?>
    </div>
    <div class="clear"></div>
    <div class="content">
        <?=$this->content?>
    </div>
</div>