<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Class CloudSearchResult
 *
 * @package PHoogkamer\CloudSearchWrapper
 */
class CloudSearchResult {

    /**
     * @var int
     */
    private $amountOfHits;

    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $time;

    /**
     * @var array
     */
    private $hits;

    /**
     * @var array
     */
    private $facets;

    /**
     * @param $amountOfHits
     * @param $start
     * @param $time
     * @param $facets
     */
    public function __construct($amountOfHits, $start, $time, $facets)
    {
        $this->amountOfHits = $amountOfHits;
        $this->start = $start;
        $this->time = $time;
        $this->facets = $facets;
    }

    /**
     * @return int
     */
    public function getAmountOfHits()
    {
        return $this->amountOfHits;
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return array
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @param array $hits
     * @param $resultDocumentClass
     * @throws \Exception
     */
    public function fillWithHits(array $hits, $resultDocumentClass)
    {
        $this->hits = [];

        foreach ($hits as $hit)
        {
            /* @var $document CloudSearchDocumentInterface */
            $document = new $resultDocumentClass();

            if(!($document instanceof CloudSearchDocumentInterface))
            {
                throw new \Exception($resultDocumentClass . ' must implement CloudSearchDocumentInterface');
            }

            $document->fillWithHit($hit);

            $this->hits[] = $document;
        }
    }
}