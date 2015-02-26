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
     * @param $amountOfHits
     * @param $start
     * @param $time
     */
    public function __construct($amountOfHits, $start, $time)
    {
        $this->amountOfHits = $amountOfHits;
        $this->start = $start;
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getAmountOfHits()
    {
        return $this->amountOfHits;
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