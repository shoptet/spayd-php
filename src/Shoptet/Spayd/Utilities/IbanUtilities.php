<?php

namespace Shoptet\Spayd\Utilities;

use Shoptet\Spayd\Exceptions;

class IbanUtilities {
	
	public static function computeIbanFromBankAccount(\Shoptet\Spayd\Model\DefaultAccount $account) {
		if ($account->isValid()) {
			$prefix = sprintf('%06d', $account->getPrefix());
			$accountNumber = sprintf('%010d', $account->getAccountNumber());
			$bankCode = sprintf('%04d', $account->getBankCode());
			
			$accountBuffer = $bankCode . $prefix . $accountNumber . $account->getNumericLanguageCode();
			$checksum = sprintf('%02d', (98 - bcmod($accountBuffer, 97)));

			// build the IBAN number
			return $account->getLocale() . $checksum . $bankCode . $prefix . $accountNumber;
		} else {
			return FALSE;
		}
	}
}