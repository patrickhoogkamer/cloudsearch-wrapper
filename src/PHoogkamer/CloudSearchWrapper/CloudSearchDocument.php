<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Class CloudSearchDocument
 */
class CloudSearchDocument {

	private $id;

	private $fields;

	private $type;

	//TODO magic getter and setter for individual fields

	/**
	 * @param $id
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * @param array $fields
	 * @param bool  $filterNullFields
	 */
	public function setFields(array $fields, $filterNullFields = true)
	{
		if($filterNullFields)
		{
			$fields = array_filter($fields);
		}

		$this->fields = $fields;
	}

	public function setTypeAdd()
	{
		$this->type = 'add';
	}

	public function setTypeDelete()
	{
		$this->type = 'delete';
	}

	/**
	 * @return array
	 */
	public function getDocument()
	{
		$document = [
			'type'		=> $this->type,
			'id'		=> $this->id,
			'fields'	=> $this->fields
		];

		return array_filter($document);
	}
}