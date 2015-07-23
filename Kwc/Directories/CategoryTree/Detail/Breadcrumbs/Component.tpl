<div class="<?=$this->rootElementClass;?>">
    <?= $this->placeholder['currentCategories']; ?>
    <? if ($this->root) { ?>
    <?= $this->componentLink($this->root, $this->placeholder['categoryTreeRootText']); ?>
    &raquo;
    <? } ?>
    <? $i = 0;
    foreach ($this->breadcrumbs as $bc) { ?>
        <? if ($i++ >= 1) echo '&raquo;'; ?>
        <?= $this->componentLink($bc); ?>
    <? } ?>
</div>
