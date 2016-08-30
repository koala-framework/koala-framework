<div class="<?=$this->rootElementClass;?>">
    <?= $this->placeholder['currentCategories']; ?>
    <?php if ($this->root) { ?>
    <?= $this->componentLink($this->root, $this->placeholder['categoryTreeRootText']); ?>
    &raquo;
    <?php } ?>
    <?php $i = 0;
    foreach ($this->breadcrumbs as $bc) { ?>
        <?php if ($i++ >= 1) echo '&raquo;'; ?>
        <?= $this->componentLink($bc); ?>
    <?php } ?>
</div>
