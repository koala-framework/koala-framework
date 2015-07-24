<? if ($this->referers) { ?>
    <div class="<?=$this->rootElementClass?>">
        <h3 class="refererFound"><?= $this->placeholder['header']; ?></h3>
        <ul>
            <?  $i = 0;
            foreach ($this->referers as $v) { ?>
                <? if (!strpos($v['query'], 'url')){?>
                    <li class="<? if($i++ == 0) echo 'first'; ?>">
                        <a href="http://<?= htmlspecialchars($v['host']); ?>/search?q=<?= htmlspecialchars($v['query']); ?>" data-kwc-popup="blank"><?= htmlspecialchars($v['host']); ?></a>
                        <? if ($v['query']) { ?>
                            mit Suche nach
                            <a href="http://<?= htmlspecialchars($v['host']); ?>/search?q=<?= htmlspecialchars($v['query']); ?>" data-kwc-popup="blank"><?= htmlspecialchars($v['query']); ?></a>
                        <? } ?>
                    </li>
                <? } else {?>
                    <li class="<? if($i++ == 0) echo 'first'; ?>">
                        <a href="<?= htmlspecialchars($v['row']->referer_url); ?>" data-kwc-popup="blank"><?= htmlspecialchars($v['host']); ?></a>
                        <? if ($v['query']) { ?>
                            mit Suche nach
                            <a href="<?= htmlspecialchars($v['row']->referer_url);?>" data-kwc-popup="blank"><?= htmlspecialchars($v['query']); ?></a>
                        <? } ?>
                    </li>
                <? } ?>
            <? } ?>
        </ul>
    </div>
<? } ?>
