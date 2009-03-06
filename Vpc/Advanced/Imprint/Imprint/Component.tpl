<div class="<?=$this->cssClass?>">
    <? if($this->row->company || $this->row->name || $this->row->address || $this->row->zipcode || $this->row->city) {?>
        <span class="imprintHeadline"><?=trlVps('Operating company / responsible person for the content');?></span>
        <p>
            <? if($this->row->company) echo $this->row->company."<br/>";?>
            <? if($this->row->name) echo $this->row->name."<br/>";?>
            <? if($this->row->address) echo $this->row->address."<br/>";?>
            <? if($this->row->zipcode) echo $this->row->zipcode;?><? if($this->row->city) echo " ".$this->row->city;?>
        </p>
    <? } ?>
    <? if($this->row->fon || $this->row->fax || $this->row->mobile) {?>
        <p>
            <? if($this->row->fon) echo trlVps('Fon').": ".$this->row->fon."<br/>";?>
            <? if($this->row->fax) echo trlVps('Fax').": ".$this->row->fax."<br/>";?>
            <? if($this->row->mobile) echo trlVps('Mobile').": ".$this->row->mobile;?>
        </p>
    <? } ?>
    <? if($this->row->email || $this->row->website) {?>
        <p>
            <? if($this->row->email) echo "<a href='mailto:".$this->row->email."'>".$this->row->email."</a><br/>"?>
            <? if($this->row->website) echo "<a href='".$this->row->website."' rel='popup_blank'>".$this->row->website."</a>"?>
        </p>
    <? } ?>
    <? if($this->row->crn) {?>
        <span class="imprintHeadline"><?=trlVps('Commercial register number');?></span>
        <p>
            <?=$this->row->crn;?>
        </p>
    <? } ?>
    <? if($this->row->register_court) {?>
        <span class="imprintHeadline"><?=trlVps('Register court');?></span>
        <p>
            <?=$this->row->register_court;?>
        </p>
    <? } ?>
    <? if($this->row->court) {?>
        <span class="imprintHeadline"><?=trlVps('Court');?></span>
        <p>
            <?=$this->row->court;?>
        </p>
    <? } ?>
    <? if($this->row->uid_number) {?>
        <span class="imprintHeadline"><?=trlVps('Purchase tax-identification number');?></span>
        <p>
            <?=$this->row->uid_number;?>
        </p>
    <? } ?>
    <? if($this->row->bank_data || $this->row->bank_code || $this->row->account_number || $this->row->iban || $this->row->bic_swift) {?>
        <span class="imprintHeadline"><?=trlVps('Bank data');?></span>
        <p>
            <? if($this->row->bank_data) echo $this->row->bankData."<br/>";?>
            <? if($this->row->bank_code) echo trlVps('Bank code').": ".$this->row->bankCode."<br/>";?>
            <? if($this->row->account_number) echo trlVps('Account number').": ".$this->row->accountNumber."<br/>";?>
            <? if($this->row->iban) echo trlVps('IBAN').": ".$this->row->iban."<br/>";?>
            <? if($this->row->bic_swift) echo trlVps('BIC / SWIFT').": ".$this->row->bic_swift;?>
        </p>
    <? } ?>
    <? if($this->row->dvr_number) {?>
        <span class="imprintHeadline"><?=trlVps('DVR-Number');?></span>
        <p>
            <?=$this->row->dvr_number;?>
        </p>
    <? } ?>
    <? if($this->row->club_number_zvr) {?>
        <span class="imprintHeadline"><?=trlVps('Clubnumber ZVR');?></span>
        <p>
            <?=$this->row->club_number_zvr;?>
        </p>
    <? } ?>
    <? if($this->row->job_title) {?>
        <span class="imprintHeadline"><?=trlVps('Job title');?></span>
        <p>
            <?=$this->row->job_title;?>
        </p>
    <? } ?>
    <? if($this->row->agency) {?>
        <span class="imprintHeadline"><?=trlVps('Agency accordant §5 ECG');?></span>
        <p>
            <?=$this->row->agency;?>
        </p>
    <? } ?>
    <? if($this->row->employment_specification) {?>
        <span class="imprintHeadline"><?=trlVps('Employment specification');?></span>
        <p>
            <?=$this->row->employment_specification;?>
        </p>
    <? } ?>
    <? if($this->row->link_company_az) {?>
        <span class="imprintHeadline"><?=trlVps('Entry at WK Austria');?></span>
        <p>
            <a href="<?=$this->row->linkCompanyAZ;?>" rel="popup_blank"><?=$this->row->linkCompanyAZ;?></a>
        </p>
    <? } ?>
</div>

