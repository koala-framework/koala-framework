<? if ($this->show) { ?>
    <div class="<?=$this->cssClass?>">
    	<span><?=$this->placeholders['prefix']?></span>
		<?=$this->partials($this->data, $this->partialParams);?>
		<div class="clear"></div>
	</div>
<? } ?>