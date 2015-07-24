<div class="<?=$this->rootElementClass?>" style="width: <?=$this->config['width']?>px;">
    <div class="youtubeContainer ratio<?=$this->config['ratio']?>">
        <div class="outerLoading">
            <div class="loading"></div>
        </div>
        <div class="youtubePlayer">
            <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
        </div>
    </div>
</div>

