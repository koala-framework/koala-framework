<div class="<?=$this->cssClass?>">
    <div class="postData">
        <div class="postInfo">
            <? if (!is_string($this->user)) { ?>
                <div class="avatar">
                    <?= $this->componentLink(
                            $this->user,
                            $this->component($this->user->getChildComponent('-general')
                                ->getChildComponent('-avatar')->getChildComponent('-small'))
                    ) ?>
                </div>
                <div class="user">
                    <?=$this->data->trlKwf('By')?>: <?= $this->componentLink($this->user) ?>
                    <?=$this->component($this->user->getChildComponent('-general')->getChildComponent('-rating'))?>
                </div>
            <? } ?>
            <? if ($this->data->row->name) { ?>
                <div class="user"><?= $this->data->row->name; ?></div>
            <? } ?>
            <strong><?=$this->placeholder['prePostNumber']?><?= $this->postNumber ?></strong>
            <em>
                <?=$this->data->trlKwf('on') ?> <?=$this->date($this->data->row->create_time)?>
                <?=$this->data->trlKwf('at') ?> <?=$this->time($this->data->row->create_time)?>
            </em><br />
            <? if($this->actions) { ?>
                <?=$this->data->trlKwf('Post')?>:
                <?= $this->component($this->actions) ?>
            <? } ?>
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