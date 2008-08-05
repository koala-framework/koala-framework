<ul>
<?php foreach ($this->groups as $g) { ?>
    <li class="<?= $g->row->post == 1 ? 'post' : 'title' ?>">
        <?php if ($g->row->post) { ?>
            <div class="description">
                <?=$this->componentLink($g)?>
                <p><?= $g->row->description ?></p>
            </div>
            <!--
            <div class="lastPost">
                <div class="statistik">
                    <div class="threads"><strong>{trlVps text="Topics"}:</strong> {$g.numThreads}</div>
                    <div class="posts"><strong>{trlVps text="Entries"}:</strong> {$g.numPosts}</div>
                </div>
                <strong>{trlVps text="Last Entry"}:</strong>
                {if $g.lastPostSubject}
                    <a href="{$g.lastPostUrl}" title="{$g.lastPostSubject}">{$g.lastPostSubject|truncate:37:'...':true|htmlspecialchars}</a>
                    <div class="time">
                        <strong>am: </strong>{$g.lastPostTime|date_format:"%d.%m.%y, %H:%M"} <strong>von: </strong>
                        {if $g.lastPostUserUrl}
                            <a href="{$g.lastPostUserUrl}">{$g.lastPostUser}</a>
                        {else}
                            {$g.lastPostUser}
                        {/if}
                    </div>
                {else}
                    -
                    <div class="time">&nbsp;</div>
                {/if}
            </div>
            -->
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