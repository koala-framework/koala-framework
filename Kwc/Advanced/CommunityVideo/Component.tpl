<div class="<?=$this->rootElementClass?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <div class="communityVideoContainer ratio<?=$this->config['ratio']?>">
        <div class="outerLoading">
            <div class="loading"></div>
        </div>
        <div class="communityVideoPlayer">
            <iframe frameborder="0" src="<?=$this->url?>" width="<?=$this->row->width?>" height="<?=$this->row->height?>" allowfullscreen></iframe>
        </div>
    </div>
</div>

