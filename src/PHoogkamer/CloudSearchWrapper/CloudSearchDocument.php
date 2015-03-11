<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Class CloudSearchDocument
 */
class CloudSearchDocument implements CloudSearchDocumentInterface {

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

    /**
     * Document always needs an $id.
     *
     * @param $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setField($name, $value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->fields[$name];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->fields);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->fields;
    }

    /**
     * Set an individual field. There is an option to $filterNullFields (means it won't get added if null).
     *
     * @param $key
     * @param $value
     * @param bool $filterNullFields
     * @return bool
     */
    public function setField($key, $value, $filterNullFields = true)
    {
        if($filterNullFields && is_null($value))
        {
            return false;
        }

        $this->fields[$key] = $value;

        return true;
    }

    /**
     * @param $key
     * @param array $array
     * @return mixed
     */
    private function getValueFromArray($key, array $array)
    {
        if(isset($array[$key]))
        {
            return $array[$key];
        }

        return null;
    }

    /**
     * @param $path
     * @return mixed|null
     */
    public function getField($path)
    {
        $currentField = null;

        foreach (explode('.', $path) as $key)
        {
            $currentField = $this->getValueFromArray($key, $currentField);

            if(is_null($currentField))
            {
                return null;
            }
        }

        return $currentField;
    }

    /**
     * Set the document fields by associative array.
     *
     * @param array $fields
     * @param bool $filterNullFields
     */
    public function setFields(array $fields, $filterNullFields = true)
    {
        if($filterNullFields)
        {
            $fields = array_filter($fields, [$this, 'filterNullField']);
        }

        $this->fields = $fields;
    }

    /**
     * @param $value
     * @return bool
     */
    private function filterNullField($value)
    {
        return ! is_null($value);
    }

    /**
     * @param array $hit
     */
    public function fillWithHit(array $hit)
    {
        $this->id = $hit['id'];

        foreach ($hit['fields'] as $key => $field)
        {
            if(is_array($field) && count($field) === 1)
            {
                $this->fields[$key] = $field[0];
            }
            else
            {
                $this->fields[$key] = $field;
            }
        }
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
            'type'   => $this->type,
            'id'     => $this->id,
            'fields' => $this->fields
        ];

        return array_filter($document);
    }
}