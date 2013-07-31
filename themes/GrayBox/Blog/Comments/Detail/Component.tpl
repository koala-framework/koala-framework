<div class="<?=$this->cssClass?>" id="<?=$this->data->componentId?>">
    <div class="postData">
        <div class="postInfo">
            <? if ($this->avatar) { ?>
                <div class="avatar">
                    <? if ($this->avatar instanceof Kwf_Component_Data) { ?>
                        <?= $this->componentLink(
                                $this->user,
                                $this->component($this->avatar)
                        ) ?>
                    <? } else { ?>
                        <img src="<?=$this->avatar?>" width="68" height="68" alt="" />
                    <? } ?>
                </div>
            <? } ?>
            <? if ($this->user) { ?>
                <div class="user">
                    <?if (is_string($this->user)) {?>
                        <?=$this->user?>
                    <?} else {?>
                        <?= $this->componentLink($this->user) ?>
                    <?}?>
                </div>
            <? } ?>
            <?=$this->data->trlKwf('on') ?>
            <a href="<?=$this->data->url?>#<?=$this->data->componentId?>">
                 <?=$this->date($this->data->row->create_time)?>
                <?=$this->data->trlKwf('at') ?> <?=$this->time($this->data->row->create_time)?>
            </a>
            <?=$this->data->trlKwf('said') ?>:<br />
            <?if ($this->actions->hasContent()){?>
                <?=$this->data->trlKwf('Post')?>:
                <?= $this->component($this->actions) ?>
            <?}?>
        </div>
        <div class="text">
            <?=$this->content?>
        </div>
        <? if (isset($this->signature)) { ?>
            <?=$this->component($this->signature)?>
        <? } ?>
    </div>
    <div class="clear"></div>
</div>