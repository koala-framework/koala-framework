<div class="<?=$this->rootElementClass?>">
<input type="hidden" class="config" value="<?=htmlspecialchars(json_encode($this->config))?>">
<audio id="player2" src="<?=htmlspecialchars($this->source['src'])?>" type="<?=htmlspecialchars($this->source['type'])?>" controls="controls">
</audio>
</div>
