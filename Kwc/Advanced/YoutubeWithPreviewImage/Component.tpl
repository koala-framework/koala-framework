<div class="<?=$this->rootElementClass?> ratio<?=$this->config['ratio']?>">
    <div class="outerYoutubeContainer">
        <div class="image">
            <?=$this->component($this->previewImage)?>
            <div class="playButton">
                <div class="innerPlayButton">
                    <?=$this->data->trlKwf('Start Video')?>
                </div>
            </div>
        </div>
        <div class="youtubeContainer">
            <div class="outerLoading">
                <div class="loading"></div>
            </div>
            <div class="youtubePlayer">
                <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
            </div>
        </div>
    </div>
</div>
