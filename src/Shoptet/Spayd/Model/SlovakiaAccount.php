<?php
namespace Shoptet\Spayd\Model;
use Shoptet\Spayd\Model;

class SlovakiaAccount extends CzechAccount {

	protected $accountPattern = '~^(?P<prefixPart>(?P<prefix>\d{0,6})(?P<prefixDelimiter>\-{1})){0,1}(?P<accountPart>(?P<accountNumber>\d{2,10})(?P<bankCodeDelimiter>\/{1})(?P<bankCode>\d{3,4}|[A-Z]{4,7}))$~';
	
}
