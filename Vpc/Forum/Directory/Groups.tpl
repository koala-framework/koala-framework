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
                        <strong>am: </strong><?= $this->dateTime($g->lastPost->row->create_time) ?> <strong>von: </strong>
                        <?= $this->componentLink($g->lastUser) ?>
                        <!--
                        <strong>am: </strong>{$g.lastPostTime|date_format:"%d.%m.%y, %H:%M"} <strong>von: </strong>
                        {if $g.lastPostUserUrl}
                            <a href="{$g.lastPostUserUrl}">{$g.lastPostUser}</a>
                        {else}
                            {$g.lastPostUser}
                        {/if}
                        -->
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