<?php if ($this->threads) { ?>
    <div class="<?=$this->cssClass ?>">
        <h2>Aktuell im Forum:</h2>
        <ul>
            <?php $x= 0; foreach ($this->threads as $thread) { $x++ ?>
                <li <?php if($x == 1) { ?> class="first"<?php } ?>>
                    <div class="thread">
                        <?= $this->date($thread->lastPost->row->create_time) ?><br />
                        <?=$this->componentLink($thread, $this->truncate($thread->row->subject, 50, '...', true));?>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>