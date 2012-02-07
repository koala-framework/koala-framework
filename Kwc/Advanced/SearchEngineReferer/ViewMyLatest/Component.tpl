<? if ($this->referers) { ?>
    <div class="<?=$this->cssClass?>">
        <h3 class="refererFound"><?= $this->placeholder['header']; ?></h3>
        <ul>
            <?  $i = 0;
            foreach ($this->referers as $v) { ?>
                <li class="<? if($i++ == 0) echo 'first'; ?>">
                    <a href="http://<?= htmlspecialchars($v['host']); ?>/search?q=<?= htmlspecialchars($v['query']); ?>" rel="popup_blank"><?= htmlspecialchars($v['host']); ?></a>
                    <? if ($v['query']) { ?>
                        mit Suche nach
                        <a href="http://<?= htmlspecialchars($v['host']); ?>/search?q=<?= htmlspecialchars($v['query']); ?>" rel="popup_blank"><?= htmlspecialchars($v['query']); ?></a>
                    <? } ?>
                </li>
            <? } ?>
        </ul>
    </div>
<? } ?>
