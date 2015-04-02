<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Used to construct a structured query used by CloudSearchClient.
 *
 * Class CloudSearchStructuredQuery
 *
 * @package PHoogkamer\CloudSearchWrapper
 */
class CloudSearchStructuredQuery extends CloudSearchQuery implements CloudSearchQueryInterface{


    /**
     * @return string
     */
    public function getQueryParserType()
    {
        return 'structured';
    }

    /**
     * Set query to match all documents, this erases the previous query entries.
     */
    public function matchAll()
    {
        $this->query = 'matchall';
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

        $this->query = str_replace('(or )', '', $this->query);

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

        $this->query = str_replace('(and )', '', $this->query);

        return $this;
    }

    /**
     * Get the concatenated query. Used by CloudSearchClient::search().
     *
     * @return string
     */
    public function getQuery()
    {
        if(empty($this->query))
        {
            $this->query = 'matchall';
        }

        return $this->query;
    }
}