<?php if ($this->referers) { ?>
    <div class="<?=$this->rootElementClass?>">
        <h3 class="refererFound"><?= $this->placeholder['header']; ?></h3>
        <ul>
            <?php  $i = 0;
            foreach ($this->referers as $v) { ?>
                <li class="<?php if($i++ == 0) echo 'first'; ?>">
                    <?php if ($v['component']) echo $this->componentLink($v['component']); ?>
                    gefunden bei<br />
                    <a href="<?= Kwf_Util_HtmlSpecialChars::filter($v['row']->referer_url); ?>" data-kwc-popup="blank"><?= Kwf_Util_HtmlSpecialChars::filter($v['host']); ?></a>
                    <?php if ($v['query']) { ?>
                        mit Suche nach
                        <a href="<?= Kwf_Util_HtmlSpecialChars::filter($v['row']->referer_url); ?>" data-kwc-popup="blank"><?= Kwf_Util_HtmlSpecialChars::filter($v['query']); ?></a>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
