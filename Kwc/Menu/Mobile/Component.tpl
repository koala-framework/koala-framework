<div class="<?=$this->cssClass;?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <a class="showMenu" href="#">
        <?= $this->placeholder['menuLink'] ?>
    </a>
    <div class="slider"></div>
</div>
