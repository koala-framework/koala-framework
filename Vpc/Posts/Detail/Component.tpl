<div class="<?=$this->cssClass?>">
    <div class="postData">
        <? if ($this->user) { ?>
        <div class="postInfo">
            <div class="avatar">
                <?= $this->componentLink(
                        $this->user, 
                        $this->component($this->user->getChildComponent('-general')
                            ->getChildComponent('-avatar')->getChildComponent('-small'))
                ) ?>
            </div>
            <div class="user">
                <?=trlVps('By')?>: <?= $this->componentLink($this->user) ?>
                <?=$this->component($this->user->getChildComponent('-general')->getChildComponent('-rating'))?>
            </div>
            <? } ?>
            <strong>#<?= $this->postNumber ?></strong>
            <em><?=trlVps('on') ?> <?=$this->date($this->data->row->create_time)?>
                <?=trlVps('at') ?> <?=$this->time($this->data->row->create_time)?></em><br />
            <?=trlVps('Post')?>:
            <?= $this->component($this->actions) ?>
        </div>
        <div class="text">
            <?=$this->content?>
        </div>
        <?=$this->component($this->signature)?>
    </div>
    <div class="clear"></div>
</div>