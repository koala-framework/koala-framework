<div class="<?=$this->cssClass ?>">
    <ul>
        <?php $x= 0; foreach ($this->threads as $thread) { $x++ ?>
            <li <?php if($x == 1) { ?> class="first"<?php } ?>>
                <div class="avatar">
                    <?= $this->componentLink($thread->lastPost->user, $this->component($thread->lastPost->user->getChildComponent('-general')->getChildComponent('-avatar')->getChildComponent('-small'))) ?>
                </div>
                <div class="thread">
                    <?=$this->componentLink($thread, $this->truncate($thread->row->subject, 50, '...', true));?>
                    <span>(<?=$thread->replies ?> <?=trlpVps("reply", "replies", $thread->replies) ?>)<br />
                        <?= trlVps('Last post by') ?> <?= $this->componentLink($thread->lastPost->user) ?> <?= $this->timestamp($thread->lastPost->row->create_time) ?>
                        | <?=$this->componentLink($thread->getParentPage(), $this->truncate($thread->getParentPage()->row->name, 22, '...', true));?>
                    </span>
                </div>
            </li>
        <?php } ?>
    </ul>
</div>