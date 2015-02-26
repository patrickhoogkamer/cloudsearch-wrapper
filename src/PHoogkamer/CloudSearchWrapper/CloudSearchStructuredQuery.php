<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Used to construct a structured query used by CloudSearchClient.
 *
 * Class CloudSearchStructuredQuery
 */
class CloudSearchStructuredQuery {

    /**
     * Concatenated structured query.
     *
     * @var string
     */
    private $query;

    /**
     * The query size.
     *
     * @var int
     */
    private $size;

    /**
     * The facet fields used for the query.
     *
     * @var array
     */
    private $facet;

    /**
     * Set query to match all documents, this erases the previous query entries.
     */
    public function matchAll()
    {
        $this->query = 'matchall';
    }

    /**
     * The max result size (search returns $size items)
     *
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = (int)$size;
    }

    /**
     * Mainly used by the CloudSearchClient::search() method, gets the size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set facet array in the same way as the AWS SDK.
     *
     * @param array $facet
     */
    public function setFacet(array $facet)
    {
        $this->facet = $facet;
    }

    /**
     * @return bool
     */
    public function facetIsEmpty()
    {
        if(is_null($this->facet))
        {
            return true;
        }

        return false;
    }

    /**
     * Used by CloudSearchClient::search() to set the facet parameter.
     *
     * @return string
     */
    public function getFacet()
    {
        return json_encode($this->facet);
    }

    /**
     * Add a field to query, add string value by setting $isString as true.
     *
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
     * Implementation of the range field. $isString for string ranges.
     *
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
     * Concat the query together in a safe way.
     *
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
     * Add an OR statement to the query. Pass a closure to set the fields (or other AND/OR statements) in it.
     *
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
     * Add an AND statement to the query. Pass a closure to set the fields (or other AND/OR statements) in it.
     *
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

    /**
     * Get the concatenated query. Used by CloudSearchClient::search().
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}