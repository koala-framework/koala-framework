<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
        <?php foreach ($this->items as $item) { ?>
            <li class="threads">
                <div class="description">
                    <?=$this->componentLink(
                        $item, 
                        '<span>' . $item->replies . ' ' . trlpVps("reply", "replies", $item->replies) . '</span>' . $this->truncate($item->row->subject, 50, '...', true), 
                        'name'
                    );?>
                </div>
               
                <div class="statistik">
                    <? if ($item->firstPost) { ?>
                    <div class="threads"><strong><?= trlVps('Created by') ?>:</strong>
                        <?= $this->componentLink($item->firstPost->user) ?>
                        <div class="posts"><?= $this->timestamp($item->firstPost->row->create_time) ?></div>
                    </div>
                    <? } ?>
                </div>
    
                <div class="lastPost">
                    <? if ($item->lastPost) { ?>
                    <strong><?= trlVps('Last entry') ?>:</strong>
                    <?= $this->componentLink($item->lastPost->user) ?>
                    <div class="time"><?= $this->timestamp($item->lastPost->row->create_time) ?></div>
                    <? } ?>
                </div>
                <div class="clear"></div>
            </li>
        <?php } ?>
    </ul>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>