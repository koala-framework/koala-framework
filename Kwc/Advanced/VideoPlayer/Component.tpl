<div class="<?=$this->cssClass?>">
<input type="hidden" class="config" value="<?=htmlspecialchars(json_encode($this->config))?>">
<video <?=(!$this->config['autoPlay']) ? "preload=\"none\"" : ""?> width="<?=$this->config['videoWidth']?>" height="<?=$this->config['videoHeight']?>" <?=($this->imageUrl) ? "poster=\"".htmlspecialchars($this->imageUrl)."\"" : ""?> id="player2" controls="controls">
    <? foreach ($this->sources as $source) { ?>
        <source src="<?=htmlspecialchars($source['src'])?>" type="<?=htmlspecialchars($source['type'])?>" title="<?=htmlspecialchars($source['title'])?>">
    <? } ?>
</video>
</div>
