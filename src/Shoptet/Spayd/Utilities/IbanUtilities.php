<?php

namespace Shoptet\Spayd\Utilities;

use Shoptet\Spayd\Exceptions;

class IbanUtilities {
	
	/**
	 * @param \Shoptet\Spayd\Model\DefaultAccount $account
	 * @return FALSE|string
	 */
	public static function computeIbanFromBankAccount(\Shoptet\Spayd\Model\DefaultAccount $account) {
		if ($account->isValid()) {
			$prefix = str_pad($account->getPrefix(), 6, '0', STR_PAD_LEFT);
			$accountNumber = str_pad($account->getAccountNumber(), 10, '0', STR_PAD_LEFT);
			$bankCode = str_pad($account->getBankCode(), 4, '0', STR_PAD_LEFT);
			
			$accountBuffer = $bankCode . $prefix . $accountNumber . $account->getNumericLanguageCode();
			$checksum = sprintf('%02d', (98 - bcmod($accountBuffer, 97)));

			// build the IBAN number
			return $account->getLocale() . $checksum . $bankCode . $prefix . $accountNumber;
		} else {
			return FALSE;
		}
	}
}
