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
                    <div class="threads"><strong><?=trlVps('Topics')?>:</strong> <!--{$g.numThreads}-->1</div>
                    <div class="posts"><strong><?=trlVps('Entries')?>:</strong> <!--{$g.numPosts}-->3</div>
                </div>
                <strong><?=trlVps('Last Entry')?>:</strong>
                <? if(true || $this->TODOlastPostSubject) { ?>
                    <!--<a href="{$g.lastPostUrl}" title="{$g.lastPostSubject}">{$g.lastPostSubject|truncate:37:'...':true|htmlspecialchars}</a>-->
                    <a href="#" title="TODO">TODO subjcet....</a>
                    <div class="time">
                        <strong>am: </strong>01.1.2008 15:30 <strong>von: </strong>
                        <a href="#">TODO USER</a>
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