<?= $this->placeholder['currentCategories']; ?>
<? $i = 0;
foreach ($this->breadcrumbs as $bc) { ?>
    <? if ($i++ >= 1) echo '&raquo;'; ?>
    <?= $this->componentLink($bc); ?>
<? } ?>
