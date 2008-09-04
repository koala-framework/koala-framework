<div class="<?=$this->cssClass?>">
    <div class="postData">
        <? if ($this->user) {
            $userText = $this->user->name;
            $rating = $this->user->getChildComponent('-rating');
            if ($rating) $userText .= ' ' . $this->component($rating);
        ?>
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
            <? if ($this->edit) { ?>
                <?=$this->componentLink($this->edit)?> |
            <? } ?>
            <? if ($this->delete) { ?>
                <?=$this->componentLink($this->delete)?> |
            <? } ?>
            <?=$this->componentLink($this->report)?> | <?=$this->componentLink($this->quote)?>
        </div>
        <div class="comment">
            <?=$this->content?>
        </div>
        <? if ($this->user->row->signature) { ?>
            <p class="signature"><tt>--<br /><?=nl2br(htmlspecialchars($this->user->row->signature))?></tt></p>
        <?php } ?>
    </div>
    <div class="clear"></div>
</div>