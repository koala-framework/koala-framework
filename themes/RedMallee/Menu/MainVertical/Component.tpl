<div class="<?=$this->cssClass;?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <a class="showMenu" href="#"><?= $this->data->trlKwf('Menu') ?></a>
</div>
