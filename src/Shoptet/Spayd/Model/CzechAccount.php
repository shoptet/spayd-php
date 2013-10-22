<?php
namespace Shoptet\Spayd\Model;
use Shoptet\Spayd\Model;

class CzechAccount extends DefaultAccount {

	const PREFIX_DELIMITER = '-';
	const BANK_CODE_DELIMITER = '/';
	
	private $accountString;
	private $czechRegularExpression = '~^(?P<prefixPart>(?P<prefix>\d{0,6})(?P<prefixDelimiter>\-{1})){0,1}(?P<accountPart>(?P<accountNumber>\d{2,10})(?P<bankCodeDelimiter>\/{1})(?P<bankCode>\d{3,4}))$~';
	
	private $prefix;
	private $accountNumber;
	private $bankCode;
	
	public function getBankCode() {
		return $this->bankCode;
	}

	public function getAccountNumber() {
		return $this->accountNumber;
	}

	public function getPrefix() {
		return $this->prefix;
	}

	public function isValid() {
		return (bool) preg_match($this->czechRegularExpression, $this->accountString);
	}
	
	protected function buildAccountParts() {
		preg_match($this->czechRegularExpression, $this->accountString, $accountParts);
		if (empty($accountParts['prefix']) === FALSE) {
			$this->prefix = $accountParts['prefix'];
		}
		
		if (empty($accountParts['accountNumber']) === FALSE) {
			$this->accountNumber = $accountParts['accountNumber'];
		}
		
		if (empty($accountParts['bankCode']) === FALSE) {
			$this->bankCode = $accountParts['bankCode'];
		}
	}
}