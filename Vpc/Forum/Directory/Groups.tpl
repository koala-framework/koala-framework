<ul>
<?php foreach ($this->groups as $g) { ?>
    <li class="<?= $g->row->post == 1 ? 'post' : 'title' ?>">
        <?php if ($g->row->post) { ?>
            <div class="description">
                <?=$this->componentLink($g)?>
                <p><?= $g->row->description ?></p>
            </div>

            <div class="lastPost">
                <div class="statistik">
                    <div class="threads"><strong><?=trlVps('Threads')?>: </strong><?= $g->countThreads ?></div>
                    <div class="posts"><strong><?=trlVps('Posts')?>: </strong><?= $g->countPosts ?></div>
                </div>
                <strong><?=trlVps('Last Entry')?>:</strong>
                <? if ($g->lastPost) { ?>
                    <?=$this->componentLink(
                        $g->lastPost->getPage(), 
                        $this->truncate($g->lastPost->getPage()->row->subject, 50, '...', true) 
                    );?>
                    <div class="time">
                        am: <?= $this->dateTime($g->lastPost->row->create_time) ?> von: 
                        <?= $this->componentLink($g->lastUser); ?>
                    </div>
                <? } else { ?>
                    -
                    <div class="time">&nbsp;</div>
                <? } ?>
            </div>

        <?php } else { ?>
            <?= $g->name ?>
        <?php } ?>
        <?php if (!empty($g->childGroups)) {
            echo $this->partial($this->groupsTemplate, array('groups' => $g->childGroups,
                                            'groupsTemplate' => $this->groupsTemplate));
        }
        ?>
    </li>
<?php } ?>
</ul>