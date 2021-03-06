<?php declare(strict_types = 1);

namespace PHPStan\Parser;

class CachedParser implements Parser
{

	private \PHPStan\Parser\Parser $originalParser;

	/** @var array<string, \PhpParser\Node\Stmt[]> */
	private array $cachedNodesByFile = [];

	private int $cachedNodesByFileCount = 0;

	private int $cachedNodesByFileCountMax;

	/** @var array<string, \PhpParser\Node\Stmt[]>*/
	private array $cachedNodesByString = [];

	private int $cachedNodesByStringCount = 0;

	private int $cachedNodesByStringCountMax;

	public function __construct(
		Parser $originalParser,
		int $cachedNodesByFileCountMax,
		int $cachedNodesByStringCountMax
	)
	{
		$this->originalParser = $originalParser;
		$this->cachedNodesByFileCountMax = $cachedNodesByFileCountMax;
		$this->cachedNodesByStringCountMax = $cachedNodesByStringCountMax;
	}

	/**
	 * @param string $file path to a file to parse
	 * @return \PhpParser\Node\Stmt[]
	 */
	public function parseFile(string $file): array
	{
		if ($this->cachedNodesByFileCountMax !== 0 && $this->cachedNodesByFileCount >= $this->cachedNodesByFileCountMax) {
			$this->cachedNodesByFile = array_slice(
				$this->cachedNodesByFile,
				1,
				null,
				true
			);

			--$this->cachedNodesByFileCount;
		}

		if (!isset($this->cachedNodesByFile[$file])) {
			$this->cachedNodesByFile[$file] = $this->originalParser->parseFile($file);
			$this->cachedNodesByFileCount++;
		}

		return $this->cachedNodesByFile[$file];
	}

	/**
	 * @param string $sourceCode
	 * @return \PhpParser\Node\Stmt[]
	 */
	public function parseString(string $sourceCode): array
	{
		if ($this->cachedNodesByStringCountMax !== 0 && $this->cachedNodesByStringCount >= $this->cachedNodesByStringCountMax) {
			$this->cachedNodesByString = array_slice(
				$this->cachedNodesByString,
				1,
				null,
				true
			);

			--$this->cachedNodesByStringCount;
		}

		if (!isset($this->cachedNodesByString[$sourceCode])) {
			$this->cachedNodesByString[$sourceCode] = $this->originalParser->parseString($sourceCode);
			$this->cachedNodesByStringCount++;
		}

		return $this->cachedNodesByString[$sourceCode];
	}

	public function getCachedNodesByFileCount(): int
	{
		return $this->cachedNodesByFileCount;
	}

	public function getCachedNodesByFileCountMax(): int
	{
		return $this->cachedNodesByFileCountMax;
	}

	public function getCachedNodesByStringCount(): int
	{
		return $this->cachedNodesByStringCount;
	}

	public function getCachedNodesByStringCountMax(): int
	{
		return $this->cachedNodesByStringCountMax;
	}

	/**
	 * @return array<string, \PhpParser\Node[]>
	 */
	public function getCachedNodesByFile(): array
	{
		return $this->cachedNodesByFile;
	}

	/**
	 * @return array<string, \PhpParser\Node[]>
	 */
	public function getCachedNodesByString(): array
	{
		return $this->cachedNodesByString;
	}

}
