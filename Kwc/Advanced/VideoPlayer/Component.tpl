<div class="<?=$this->cssClass?>">
<input type="hidden" class="config" value="<?=htmlspecialchars(json_encode($this->config))?>">
<video width="360" height="203" id="player2" controls="controls">
    <?if ($this->mp4Source) {?>
        <source src="<?=$this->mp4Source?>" type="video/mp4" title="mp4">
    <?}?>
    <?if ($this->webmSource) {?>
        <source src="<?=$this->webmSource?>" type="video/webm" title="webm">
    <?}?>
    <?if ($this->oggSource) {?>
        <source src="<?=$this->oggSource?>" type="video/ogg" title="ogg">
    <?}?>
</video>
</div>
