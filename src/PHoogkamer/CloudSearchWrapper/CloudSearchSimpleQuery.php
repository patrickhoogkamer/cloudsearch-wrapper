<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Used to construct a simple query used by CloudSearchClient.
 *
 * Class CloudSearchSimpleQuery
 *
 * @package PHoogkamer\CloudSearchWrapper
 */
class CloudSearchSimpleQuery extends CloudSearchQuery implements CloudSearchQueryInterface{


    /**
     * @return string
     */
    public function getQueryParserType()
    {
        return 'simple';
    }

    /**
     * @param $term
     */
    public function setTerm($term)
    {
        $this->query = $term;
    }

    /**
     * Get the query. Used by CloudSearchClient::search().
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}