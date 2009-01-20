<div class="<?=$this->cssClass?>">
    <div class="description">
        <?=$this->componentLink(
            $this->thread,
            '<span>' . $this->replies . ' '
                . trlpVps("reply", "replies", $this->replies) . '</span>'
                . $this->truncate($this->thread->row->subject, 50, '...', true),
            'name'
        );?>
    </div>

    <div class="statistik">
        <? if ($this->firstPost) { ?>
        <div class="thread"><strong><?= trlVps('Created by') ?>:</strong>
            <?= $this->componentLink($this->firstPost->user) ?>
            <div class="posts"><?= $this->timestamp($this->firstPost->row->create_time) ?></div>
        </div>
        <? } ?>
    </div>

    <div class="lastPost">
        <? if ($this->lastPost) { ?>
        <strong><?= trlVps('Last Entry') ?>:</strong>
        <?= $this->componentLink($this->lastPost->user) ?>
        <div class="time"><?= $this->timestamp($this->lastPost->row->create_time) ?></div>
        <? } ?>
    </div>
    <div class="clear"></div>
</div>