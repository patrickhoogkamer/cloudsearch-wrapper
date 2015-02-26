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
    private $size;

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
     * @param $size
     * @param $start
     * @param $time
     */
    public function __construct($size, $start, $time)
    {
        $this->size = $size;
        $this->start = $start;
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
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