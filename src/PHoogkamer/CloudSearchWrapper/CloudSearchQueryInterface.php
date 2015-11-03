<?php

namespace PHoogkamer\CloudSearchWrapper;

/**
 * Interface CloudSearchQueryInterface
 *
 * @package PHoogkamer\CloudSearchWrapper
 */
interface CloudSearchQueryInterface
{

    /**
     * @return string
     */
    public function getQueryParserType();

    /**
     * @return string
     */
    public function getQuery();

    /**
     * @param int $start
     * @return void
     */
    public function setStart($start);

    /**
     * @return int
     */
    public function getStart();

    /**
     * @param int $size
     * @return void
     */
    public function setSize($size);

    /**
     * @return int
     */
    public function getSize();

    /**
     * @param array|object $facet
     * @return void
     */
    public function setFacet($facet);

    /**
     * @return string
     */
    public function getFacet();

    /**
     * @param string $sort
     * @return void
     */
    public function setSort($sort);

    /**
     * @return string
     */
    public function getSort();

    /**
     * @param $cursor
     */
    public function setCursor($cursor);

    /**
     * @return string
     */
    public function getCursor();

    /**
     * @param bool|true $shouldUseCursor
     */
    public function useCursor($shouldUseCursor = true);

    /**
     * @return bool
     */
    public function facetIsEmpty();
}