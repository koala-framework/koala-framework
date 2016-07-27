<?php if ($this->referers) { ?>
    <div class="<?=$this->rootElementClass?>">
        <h3 class="refererFound"><?= $this->placeholder['header']; ?></h3>
        <ul>
            <?php $i = 0;
            foreach ($this->referers as $v) { ?>
                <?php if (!strpos($v['query'], 'url')){ ?>
                    <li class="<?php if($i++ == 0) echo 'first'; ?>">
                        <a href="http://<?= htmlspecialchars($v['host']); ?>/search?q=<?= htmlspecialchars($v['query']); ?>" data-kwc-popup="blank"><?= htmlspecialchars($v['host']); ?></a>
                        <?php if ($v['query']) { ?>
                            mit Suche nach
                            <a href="http://<?= htmlspecialchars($v['host']); ?>/search?q=<?= htmlspecialchars($v['query']); ?>" data-kwc-popup="blank"><?= htmlspecialchars($v['query']); ?></a>
                        <?php } ?>
                    </li>
                <?php } else { ?>
                    <li class="<?php if($i++ == 0) echo 'first'; ?>">
                        <a href="<?= htmlspecialchars($v['row']->referer_url); ?>" data-kwc-popup="blank"><?= htmlspecialchars($v['host']); ?></a>
                        <?php if ($v['query']) { ?>
                            mit Suche nach
                            <a href="<?= htmlspecialchars($v['row']->referer_url);?>" data-kwc-popup="blank"><?= htmlspecialchars($v['query']); ?></a>
                        <?php } ?>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
