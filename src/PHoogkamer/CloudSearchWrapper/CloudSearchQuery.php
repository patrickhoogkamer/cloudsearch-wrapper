<?php

namespace PHoogkamer\CloudSearchWrapper;

/**
 * Class CloudSearchQuery
 *
 * @package PHoogkamer\CloudSearchWrapper
 */
abstract class CloudSearchQuery implements CloudSearchQueryInterface
{

    /**
     * Concatenated structured query.
     *
     * @var string
     */
    protected $query;

    /**
     * The query size.
     *
     * @var int
     */
    private $size = 10;

    /**
     * The query offset
     *
     * @var int
     */
    private $start = 0;

    /**
     * The facet fields used for the query.
     *
     * @var array
     */
    private $facet;

    /**
     * @var string
     */
    private $cursor;

    /**
     * @var string
     */
    private $sort = '_score desc';

    public abstract function getQueryParserType();

    /**
     * The max result size (search returns $size items)
     *
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = (int) $size;
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
     * The query offset (search returns items starting at $start)
     *
     * @param int $start
     */
    public function setStart($start)
    {
        $this->start = (int) $start;
    }

    /**
     * Mainly used by the CloudSearchClient::search() method, gets the start.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set facet array in the same way as the AWS SDK.
     *
     * @param array|object $facet
     */
    public function setFacet($facet)
    {
        $this->facet = $facet;
    }

    /**
     * @return bool
     */
    public function facetIsEmpty()
    {
        if (is_null($this->facet)) {
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
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    public function setCursor($cursor)
    {
        $this->cursor = $cursor;
    }

    public function getCursor()
    {
        return $this->cursor;
    }

    public function useCursor($shouldUseCursor = true)
    {
        if ($shouldUseCursor && empty($this->cursor)) {
            $this->cursor = 'initial';
        } else {
            $this->cursor = null;
        }
    }

    /**
     * Get the query. Used by CloudSearchClient::search().
     *
     * @return string
     */
    public abstract function getQuery();

}