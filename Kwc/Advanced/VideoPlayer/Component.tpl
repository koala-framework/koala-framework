<div class="<?=$this->cssClass?>">
<input type="hidden" class="config" value="<?=htmlspecialchars(json_encode($this->config))?>">
<video width="<?=$this->config['videoWidth']?>" height="<?=$this->config['videoHeight']?>" id="player2" controls="controls">
    <? foreach ($this->sources as $source) { ?>
        <source src="<?=htmlspecialchars($source['src'])?>" type="<?=htmlspecialchars($source['type'])?>" title="<?=htmlspecialchars($source['title'])?>">
    <? } ?>
</video>
</div>
