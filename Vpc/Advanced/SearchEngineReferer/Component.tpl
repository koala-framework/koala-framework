<div class="<?=$this->cssClass?>">
    <ul>
        <? foreach ($this->referers as $v) { ?>
            <li>
                <a href="<?= $v['referer']; ?>" rel="popup_blank"><?= $v['host']; ?></a>
                <? if ($v['query']) { ?>
                    mit Suche nach
                    <a href="<?= $v['referer']; ?>" rel="popup_blank"><?= $v['query']; ?></a>
                <? } ?>
            </li>
        <? } ?>
    </ul>
</div>
