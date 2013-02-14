<div class="<?=$this->cssClass?> kwfSwitchHoverFade">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <div class="link switchLink">
        <a href="#">[FAV]</a>
    </div>
    <div class="switchContent">
        <?=$this->favouriteText?>
    </div>
</div>
