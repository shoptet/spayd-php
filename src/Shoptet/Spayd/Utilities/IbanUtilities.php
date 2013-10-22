<?php

namespace Shoptet\Spayd\Utilities;

use Shoptet\Spayd\Exceptions;

class IbanUtilities {
	
	public static function computeIBANFromCzechBankAccount(\Shoptet\Spayd\Model\CzechAccount $czechAccount) {
		if ($czechAccount->isValid()) {
			$prefix = sprintf('%06d', $czechAccount->getPrefix());
			$accountNumber = sprintf('%010d', $czechAccount->getAccountNumber());
			$bankCode = sprintf('%04d', $czechAccount->getBankCode());
			
			$accountBuffer = $bankCode . $prefix . $accountNumber . $czechAccount->getNumericLanguageCode();
			$checksum = sprintf('%02d', (98 - bcmod($accountBuffer, 97)));

			// build the IBAN number
			return 'CZ' . $checksum . $bankCode . $prefix . $accountNumber;
		} else {
			return FALSE;
		}
	}
}