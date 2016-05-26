<?php

namespace PHoogkamer\CloudSearchWrapper;

use Aws\CloudSearchDomain\CloudSearchDomainClient;
use Closure;

/**
 * Class CloudSearchClient
 */
class CloudSearchClient
{

    /**
     * @var CloudSearchDomainClient
     */
    private $pushClient;

    /**
     * @var CloudSearchDomainClient
     */
    private $searchClient;

    /**
     * Instantiate the private default CloudSearchDomainClient. You might need to have both a search and push client
     * because they use different endpoints. The one you instantiate through the constructor will be set as both. If you
     * do need two different clients you can set either the pushClient or searchClient afterwards with the getters and
     * setters.
     *
     * @param $endpoint
     * @param $key
     * @param $secret
     */
    public function __construct($endpoint, $key, $secret)
    {
        $this->pushClient = CloudSearchDomainClient::factory([
            'endpoint' => $endpoint,
            'key'      => $key,
            'secret'   => $secret,
            'version'  => '2013-01-01'
        ]);

        $this->searchClient = $this->pushClient;
    }

    /**
     * Set if you want to use a different client/endpoint for pushing than the one inserted in the constructor.
     *
     * @param $endpoint
     * @param $key
     * @param $secret
     */
    public function setPushClient($endpoint, $key, $secret)
    {
        $this->pushClient = CloudSearchDomainClient::factory([
            'endpoint' => $endpoint,
            'key'      => $key,
            'secret'   => $secret,
            'version'  => '2013-01-01'
        ]);
    }

    /**
     * Set if you want to use a different client/endpoint for pushing than the one inserted in the constructor.
     *
     * @param $endpoint
     * @param $key
     * @param $secret
     */
    public function setSearchClient($endpoint, $key, $secret)
    {
        $this->searchClient = CloudSearchDomainClient::factory([
            'base_url' => $endpoint,
            'key'      => $key,
            'secret'   => $secret
        ]);
    }

    /**
     * @param CloudSearchQueryInterface  $query
     * @param CloudSearchStructuredQuery $filterQuery
     * @param string                     $resultDocument
     * @return CloudSearchResult
     */
    public function search(
        CloudSearchQueryInterface $query,
        CloudSearchStructuredQuery $filterQuery = null,
        $resultDocument = '\PHoogkamer\CloudSearchWrapper\CloudSearchDocument'
    ) {
        $arguments = $this->prepareArguments($query, $filterQuery);

        $result = $this->convertResult($this->searchClient->search($arguments), $resultDocument);

        return $result;
    }

    /**
     * Iterates over results from a query. If $shouldIterate is true then will page until there are no results left.
     * Even able to resume where left of (if same $query object is used), for example when closure returns false.
     *
     * @param Closure                         $closure
     * @param CloudSearchQueryInterface       $query
     * @param CloudSearchStructuredQuery|null $filterQuery
     * @param bool|false                      $shouldIterate
     * @param string                          $resultDocument
     */
    public function loop(
        Closure $closure,
        CloudSearchQueryInterface $query,
        CloudSearchStructuredQuery $filterQuery = null,
        $shouldIterate = false,
        $resultDocument = '\PHoogkamer\CloudSearchWrapper\CloudSearchDocument'
    ) {
        if($shouldIterate) {
            $query->setStart(null);

            if(is_null($query->getCursor())) {
                $query->useCursor();

                $cursor = 'initial';
            }

            do {
                $result = $this->searchWithCursor($query, $filterQuery, $resultDocument, $cursor);

                if($result->getNextCursor() != $cursor) {
                    $cursor = $result->getNextCursor();
                } else {
                    $cursor = false;
                }

                $continueLooping = $this->loopOverHits($closure, $result);

                if(isset($continueLooping) && $continueLooping === false) {
                    return;
                }
            } while($cursor);
        } else {
            $result = $this->search($query, $filterQuery, $resultDocument);

            $this->loopOverHits($closure, $result);
        }
    }

    /**
     * @param \Guzzle\Service\Resource\Model $awsResult
     * @param                                $resultDocument
     * @return CloudSearchResult
     * @throws \Exception
     */
    private function convertResult(\Guzzle\Service\Resource\Model $awsResult, $resultDocument)
    {
        $time         = $awsResult->getPath('status/timems');
        $amountOfHits = $awsResult->getPath('hits/found');
        $start        = $awsResult->getPath('hits/start');
        $cursor       = $awsResult->getPath('hits/cursor');
        $facets       = $awsResult->getPath('facets');

        $result = new CloudSearchResult($amountOfHits, $start, $time, $facets, $cursor);

        $result->fillWithHits($awsResult->getPath('hits/hit'), $resultDocument);

        return $result;
    }

    /**
     * Push a CloudSearchDocument to CloudSearch.
     *
     * @param CloudSearchDocument $document
     */
    public function pushDocument(CloudSearchDocument $document)
    {
        $this->uploadDocuments([$document->getDocument()]);
    }

    /**
     * Push an array of CloudSearchDocuments to CloudSearch.
     *
     * @param array $documents
     * @throws \Exception
     */
    public function pushDocuments(array $documents)
    {
        $arrayDocuments = [];

        /* @var $document CloudSearchDocument */
        foreach ($documents as $document) {
            if ( ! ($document instanceof CloudSearchDocument)) {
                throw new \Exception('$documents must be an array of CloudSearchDocuments');
            }

            $arrayDocuments[] = $document->getDocument();
        }

        $this->uploadDocuments($arrayDocuments);
    }

    /**
     * Upload the documents.
     *
     * @param $documents
     */
    private function uploadDocuments($documents)
    {
        $args = [
            'contentType' => 'application/json',
            'documents'   => json_encode($documents)
        ];

        $this->pushClient->uploadDocuments($args);
    }

    /**
     * @param CloudSearchQueryInterface  $query
     * @param CloudSearchStructuredQuery $filterQuery
     * @return array
     */
    private function prepareArguments(CloudSearchQueryInterface $query, CloudSearchStructuredQuery $filterQuery = null)
    {
        $arguments = [
            'queryParser' => $query->getQueryParserType(),
            'query'       => $query->getQuery(),
            'start'       => $query->getStart(),
            'size'        => $query->getSize(),
            'sort'        => $query->getSort(),
            'queryOptions'=> $query->getQueryOptions()
        ];

        $facet = $query->getFacet();

        if ( ! $query->facetIsEmpty()) {
            $arguments['facet'] = $facet;
        }

        if ( ! is_null($filterQuery)) {
            $arguments['filterQuery'] = $filterQuery->getQuery();
        }

        $cursor = $query->getCursor();
        if ( ! is_null($cursor)) {
            $arguments['cursor'] = $cursor;
        }

        $arguments = array_filter($arguments);

        return $arguments;
    }

    /**
     * @param Closure $closure
     * @param         $result
     * @return mixed
     */
    private function loopOverHits(Closure $closure, $result)
    {
        foreach ($result->getHits() as $hit) {
            //If return is false: continue current set, then stop
            $continueLooping = $closure($hit);
        }

        return $continueLooping;
    }

    /**
     * @param CloudSearchQueryInterface $query
     * @param CloudSearchStructuredQuery $filterQuery
     * @param                            $resultDocument
     * @param                            $cursor
     * @return CloudSearchResult
     */
    private function searchWithCursor(
        CloudSearchQueryInterface $query,
        CloudSearchStructuredQuery $filterQuery,
        $resultDocument,
        $cursor
    ) {
        if ($cursor != 'initial') {
            $query->setCursor($cursor);
        }
        $result = $this->search($query, $filterQuery, $resultDocument);

        return $result;
    }
}