<div class="<?=$this->cssClass?>">
    <div class="youtubeContainer" style="width: <?=$this->config['width']?>px;height: <?=$this->config['height']?>px">
        <div class="outerLoading">
            <div class="loading"></div>
        </div>
        <div class="youtubePlayer">
            <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
        </div>
    </div>
</div>

