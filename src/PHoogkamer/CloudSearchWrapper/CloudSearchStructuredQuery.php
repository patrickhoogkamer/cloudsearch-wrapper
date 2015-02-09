<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Class CloudSearchStructuredQuery
 */
class CloudSearchStructuredQuery {

	private $query;

	/**
	 * @var int
	 */
	private $size;

	/**
	 *
	 */
	public function __construct()
	{

	}

	public function matchAll()
	{
		$this->query = 'matchall';
	}

	/**
	 * @param int $size
	 */
	public function setSize($size)
	{
		$this->size = (int) $size;
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @param      $key
	 * @param      $value
	 * @param bool $isString
	 */
	public function addField($key, $value, $isString = false)
	{
		if($isString)
		{
			$value = "'{$value}'";
		}

		$field = $key . ':' . $value;

		$this->concatField($field);
	}

	/**
	 * @param      $key
	 * @param null $from
	 * @param null $to
	 * @param bool $isString
	 */
	public function addRangeField($key, $from = null, $to = null, $isString = false)
	{
		$field = $key . ':';

		if(is_null($from))
		{
			$field .= '{';
		}
		else
		{
			if($isString)
			{
				$from = "'{$from}'";
			}

			$field .= '[' . $from;
		}

		$field .= ',';

		if(is_null($to))
		{
			$field .= '}';
		}
		else
		{
			if($isString)
			{
				$to = "'{$to}'";
			}
			$field .= $to . ']';
		}

		$this->concatField($field);
	}

	/**
	 * @param $field
	 */
	private function concatField($field)
	{
		$lastChar = mb_substr($this->query, -1);

		if($lastChar != '(' && $lastChar != ' ')
		{
			$field = ' ' . $field;
		}

		$this->query .= $field;
	}

	/**
	 * @param callable $function
	 * @return $this
	 */
	public function addOr(\Closure $function)
	{
		$this->query .= '(or ';

		$function($this);

		$this->query .= ')';

		return $this;
	}

	/**
	 * @param callable $function
	 * @return $this
	 */
	public function addAnd(\Closure $function)
	{
		$this->query .= '(and ';

		$function($this);

		$this->query .= ')';

		return $this;
	}

	public function getQuery()
	{
		return $this->query;
	}
}