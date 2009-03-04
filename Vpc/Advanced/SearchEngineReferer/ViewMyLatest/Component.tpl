<? if ($this->referers) { ?>
    <div class="<?=$this->cssClass?>">
        <h3 class="refererFound"><?= $this->placeholder['header']; ?></h3>
        <ul>
            <?  $i = 0;
            foreach ($this->referers as $v) { ?>
                <li class="<? if($i++ == 0) echo 'first'; ?>">
                    <a href="<?= $v['row']->referer_url; ?>" rel="popup_blank"><?= $v['host']; ?></a>
                    <? if ($v['query']) { ?>
                        mit Suche nach
                        <a href="<?= $v['row']->referer_url; ?>" rel="popup_blank"><?= $v['query']; ?></a>
                    <? } ?>
                </li>
            <? } ?>
        </ul>
    </div>
<? } ?>
