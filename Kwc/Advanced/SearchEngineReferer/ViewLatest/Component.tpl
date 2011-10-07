<? if ($this->referers) { ?>
    <div class="<?=$this->cssClass?>">
        <h3 class="refererFound"><?= $this->placeholder['header']; ?></h3>
        <ul>
            <?  $i = 0;
            foreach ($this->referers as $v) { ?>
                <li class="<? if($i++ == 0) echo 'first'; ?>">
                    <? if ($v['component']) echo $this->componentLink($v['component']); ?>
                    gefunden bei<br />
                    <a href="<?= htmlspecialchars($v['row']->referer_url); ?>" rel="popup_blank"><?= htmlspecialchars($v['host']); ?></a>
                    <? if ($v['query']) { ?>
                        mit Suche nach
                        <a href="<?= htmlspecialchars($v['row']->referer_url); ?>" rel="popup_blank"><?= htmlspecialchars($v['query']); ?></a>
                    <? } ?>
                </li>
            <? } ?>
        </ul>
    </div>
<? } ?>
