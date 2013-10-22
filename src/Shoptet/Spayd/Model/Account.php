<?php
namespace Shoptet\Spayd\Model;

interface Account {
	public function __construct($accountString);
	public function isValid();
	public function getPrefix();
	public function getAccountNumber();
	public function getBankCode();
}