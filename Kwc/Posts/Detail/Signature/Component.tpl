<?php if ($this->user && $this->user->row && $this->user->row->signature) { ?>
    <p class="signature"><tt>--<br /><?=nl2br(Kwf_Util_HtmlSpecialChars::filter($this->user->row->signature))?></tt></p>
<?php } ?>
