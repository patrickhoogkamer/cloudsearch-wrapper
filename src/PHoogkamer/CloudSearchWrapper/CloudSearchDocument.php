<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Class CloudSearchDocument
 */
class CloudSearchDocument {

    /**
     * The document id overwrites the document already in CloudSearch with the same id.
     *
     * @var string
     */
	private $id;

    /**
     * Associative array with fields.
     *
     * @var array
     */
	private $fields;

    /**
     * Document type, currently either 'add' or 'delete'.
     *
     * @var string
     */
	private $type;

	//TODO magic getter and setter for individual fields

	/**
     * Document always needs an $id.
     *
	 * @param $id
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
     * Set the document fields by associative array.
     *
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

    /**
     * Set the document type to 'add'.
     */
	public function setTypeAdd()
	{
		$this->type = 'add';
	}

    /**
     * Set the document type to 'delete'.
     */
	public function setTypeDelete()
	{
		$this->type = 'delete';
	}

	/**
     * Get the actual document to be pushed.
     *
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