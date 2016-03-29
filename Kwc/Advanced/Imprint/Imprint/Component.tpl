<div class="<?=$this->cssClass?>">
    <? if($this->row->company || $this->row->name || $this->row->address || $this->row->zipcode || $this->row->city) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Operating company / Responsible for content');?></span>
        <p>
            <? if($this->row->company) echo $this->row->company."<br/>";?>
            <? if($this->row->name) echo $this->row->name."<br/>";?>
            <? if($this->row->address) echo $this->row->address."<br/>";?>
            <? if($this->row->zipcode) echo $this->row->zipcode;?><? if($this->row->city) echo " ".$this->row->city;?>
        </p>
    <? } ?>
    <? if($this->row->fon || $this->row->fax || $this->row->mobile) {?>
        <p>
            <? if($this->row->fon) echo $this->data->trlKwf('Fon').": ".$this->row->fon."<br/>";?>
            <? if($this->row->fax) echo $this->data->trlKwf('Fax').": ".$this->row->fax."<br/>";?>
            <? if($this->row->mobile) echo $this->data->trlKwf('Mobile').": ".$this->row->mobile;?>
        </p>
    <? } ?>
    <? if($this->row->email || $this->row->website) {?>
        <p>
            <? if($this->row->email) echo $this->mailLink($this->row->email)."<br/>"?>
            <? if($this->row->website) echo "<a href='".$this->row->website."' data-kwc-popup='blank'>".$this->row->website."</a>"?>
        </p>
    <? } ?>
    <? if($this->row->crn) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Commercial register number');?></span>
        <p>
            <?=$this->row->crn;?>
        </p>
    <? } ?>
    <? if($this->row->register_court) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Register court');?></span>
        <p>
            <?=$this->row->register_court;?>
        </p>
    <? } ?>
    <? if($this->row->court) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Court');?></span>
        <p>
            <?=$this->row->court;?>
        </p>
    <? } ?>
    <? if($this->row->uid_number) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('VAT identification number');?></span>
        <p>
            <?=$this->row->uid_number;?>
        </p>
    <? } ?>
    <? if($this->row->bank_data || $this->row->bank_code || $this->row->account_number || $this->row->iban || $this->row->bic_swift) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Bank data');?></span>
        <p>
            <? if($this->row->bank_data) echo $this->row->bank_data."<br/>";?>
            <? if($this->row->bank_code) echo $this->data->trlKwf('Bank code').": ".$this->row->bank_code."<br/>";?>
            <? if($this->row->account_number) echo $this->data->trlKwf('Account number').": ".$this->row->account_number."<br/>";?>
            <? if($this->row->iban) echo $this->data->trlKwf('IBAN').": ".$this->row->iban."<br/>";?>
            <? if($this->row->bic_swift) echo $this->data->trlKwf('BIC / SWIFT').": ".$this->row->bic_swift;?>
        </p>
    <? } ?>
    <? if($this->row->dvr_number) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Data handling register number');?></span>
        <p>
            <?=$this->row->dvr_number;?>
        </p>
    <? } ?>
    <? if($this->row->club_number_zvr) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Clubnumber ZVR');?></span>
        <p>
            <?=$this->row->club_number_zvr;?>
        </p>
    <? } ?>
    <? if($this->row->job_title) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Job title');?></span>
        <p>
            <?=$this->row->job_title;?>
        </p>
    <? } ?>
    <? if($this->row->agency) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Agency accordant §5 ECG');?></span>
        <p>
            <?=$this->row->agency;?>
        </p>
    <? } ?>
    <? if($this->row->employment_specification) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Employment specification');?></span>
        <p>
            <?=$this->row->employment_specification;?>
        </p>
    <? } ?>
    <? if($this->row->link_company_az) {?>
        <span class="imprintHeadline"><?=$this->data->trlKwf('Entry at WK Austria');?></span>
        <p>
            <a href="<?=$this->row->link_company_az;?>" data-kwc-popup="blank"><?=$this->data->trlKwf('Company A-Z');?></a>
        </p>
    <? } ?>
</div>

