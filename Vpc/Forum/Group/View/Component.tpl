<div class="<?=$this->cssClass?>">
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
        <?php foreach ($this->items as $item) {
            $posts = $item->getChildComponents(array('generator' => 'detail'));
            $first = array_shift($posts);
            $last = array_pop($posts);
            $firstUser = $first->row->findParentRow(get_class(Vps_Registry::get('userModel')));
            $lastUser = $last->row->findParentRow(get_class(Vps_Registry::get('userModel')));
            $replies = count($posts) - 1;
            //$firstUserPage = $item->parent->parent->getChildPage(array('generator' => 'user'));
            ?>
            <li class="threads">
                <div class="description">
                    <?=$this->componentLink(
                        $item, 
                        $this->truncate($item->row->subject, 50, '...', true) . '<span>' . $replies . ' ' . trlpVps("reply", "replies", $replies) . '</span>', 
                        'name'
                    );?>
                </div>
               
                <div class="statistik">
                    <div class="threads"><strong><?= trlVps('Created by') ?>:</strong>
                        <?= $firstUser->nickname ?>
                        <div class="posts"><?= $this->dateTime($first->row->create_time, "%d.%m.%y, %H:%M") ?></div>
                    </div>
                </div>
    
                <div class="lastPost">
                <strong><?= trlVps('Last entry') ?>:</strong>
                    <?= $lastUser->nickname ?>
                    <div class="time"><?= $this->dateTime($last->row->create_time, "%d.%m.%y, %H:%M") ?></div>
                </div>
                <div class="clear"></div>
            </li>
        <?php } ?>
    </ul>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>