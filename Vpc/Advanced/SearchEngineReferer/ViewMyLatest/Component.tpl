<? if ($this->referers) { ?>
    <div class="<?=$this->cssClass?>">
        <ul>
            <? foreach ($this->referers as $v) { ?>
                <li>
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

