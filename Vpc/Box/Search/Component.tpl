<div class="<?=$this->cssClass?> searchBox">
    <form action="<?= $this->searchPageUrl; ?>" method="get" autocomplete="off">
        <input type="hidden" class="ajaxUrl" value="<?= $this->ajaxUrl; ?>" />
        <input type="text" name="query" class="searchField vpsClearOnFocus"
            value="<?= $this->placeholder['clearOnFocus']; ?>" autocomplete="off" />
        <button type="submit" class="submit"><?= $this->placeholder['searchButton']; ?></button>
    </form>
    <div class="searchResult">
        <?= $this->placeholder['initialResultText']; ?>
    </div>
</div>
