<?php

namespace Shoptet\Spayd;

use Shoptet\Spayd\Exceptions;

class Spayd
{
	const BINARY_QR_REGEXP = '[0-9A-Z $%*+-./:]';
	// Spayd part delimiter
	const DELIMITER = '*';
	// key, value delimiter
	const KV_DELIMITER = ':';

	public $appendCRC32 = TRUE;

	// default Spayd string version
	protected $version = '1.0';

	private $content = array();

	private $keysDefinition = array(
		// Account in IBAN format plus optional SWIFT
		'ACC' => '[A-Z]{2}\d{2,32}(\+[A-Z0-9]{4}[A-Z]{2}[A-Z0-9]{2,5})?',

		// One or two alternative accounts in the ACC format
		'ALT-ACC' => '[A-Z]{2}\d{2,32}(\+[A-Z0-9]{4}[A-Z]{2}[A-Z0-9]{2,5})?(,[A-Z]{2}\d{2,32}(\+[A-Z0-9]{4}[A-Z]{2}[A-Z0-9]{2,5})?)?',

		// Amount
		'AM' => '^[0-9]{0,10}(\\.[0-9]{0,2})?$',

		// Currency ISO 4217
		'CC' => '[A-Z]{3}',

		// Identificator
		'RF' => '\d{1,16}',

		// Name
		'RN' => '[0-9A-Z $%+-./:]{1,35}',

		// Date ISO 8601
		'DT' => '[12]\d{3}[01]\d[0-3]\d',

		// Type of payment
		'PT' => '[0-9A-Z $%+-./:]{1,3}',

		// Message
		'MSG' => '[0-9A-Z $%+-./:]{1,60}',

		// Control sum
		'CRC32' => '[A-F0-9]{8}',

		// Contact channel flag. P - phone, E - email
		'NT' => '[PE]',

		// Contact information. Either phone (00420123456789 or +420123456789) or email.
		// TODO: better email regexp
		'NTA' => '((\+|00)\'{12}|.+@.+\..+)',

		// Expanded params for use in Czech Republic
		'X-PER' => '\d{1,2}',
		'X-VS' => '\d{1,10}',
		'X-SS' => '\d{1,10}',
		'X-KS' => '\d{1,10}',
		'X-ID' => '[0-9A-Z $%+-./:]{1,20}',
		'X-URL' => '[0-9A-Z $%+-./:]{1,140}',
	);

	public function __construct()
	{
	}

	/*
	 * @param string $version Version of spayd
	 * @return Spayd
	 */
	public function setVersion($version)
	{
		if (!preg_match('~^\d\.\d$~', $version)) {
			throw new Exceptions\InvalidParameterException(
				"Version $version is not valid."
			);
		}

		$this->version = $version;
		return $this;
	}

	public function getVersion()
	{
		return $this->version;
	}

	/*
	 * @param string $key
	 * @param string $value
	 * @return Spayd
	 */
	public function add($key, $value)
	{
		$key = $this->normalizeKey($key);
		if (strpos($key, 'X-') !== 0 && !isset($this->keysDefinition[$key])) {
			throw new Exceptions\InvalidParameterException("Key $key is not defined in specification.");
		}

		if (isset($this->keysDefinition[$key])
			&& !preg_match('~^' . $this->keysDefinition[$key] . '$~', $value)
		) {
			throw new Exceptions\InvalidParameterException(
				"Key $key with value $value doesn't match defined format: "
				. $this->keysDefinition[$key]
			);
		}

		$this->content[$key] = $value;
		return $this;
	}

	/*
	 * @param string $key
	 * @return Spayd
	 */
	public function delete($key)
	{
		unset($this->content[$this->normalizeKey($key)]);
		return $this;
	}

	/*
	 * @param string $key
	 */
	private function normalizeKey($key)
	{
		return strtoupper($key);
	}

	public function generate()
	{
		$spayd = 'SPD'
			. self::DELIMITER
			. $this->getVersion()
			. self::DELIMITER
			. $this->implodeContent();

		if ($this->appendCRC32) {
			$spayd .= '*CRC32:' . sprintf('%x', crc32($spayd));
		}

		return $spayd;
	}

	private function implodeContent()
	{
		$this->sortContent();
		$output = '';
		foreach ($this->content as $key => $value) {
			$output .= $key . self::KV_DELIMITER . $value . self::DELIMITER;
		}

		return rtrim($output, self::DELIMITER);
	}

	private function sortContent()
	{
		ksort($this->content);
	}

	public function __toString()
	{
		return $this->generate();
	}
}