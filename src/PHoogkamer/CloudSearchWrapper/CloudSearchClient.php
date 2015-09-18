<?php

namespace PHoogkamer\CloudSearchWrapper;

use Aws\CloudSearchDomain\CloudSearchDomainClient;

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
            'base_url' => $endpoint,
            'key'      => $key,
            'secret'   => $secret
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
            'base_url' => $endpoint,
            'key'      => $key,
            'secret'   => $secret
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
        $args = [
            'queryParser' => $query->getQueryParserType(),
            'query'       => $query->getQuery(),
            'start'       => $query->getStart(),
            'size'        => $query->getSize(),
            'sort'        => $query->getSort()
        ];

        $facet = $query->getFacet();

        if ( ! $query->facetIsEmpty()) {
            $args['facet'] = $facet;
        }

        if ( ! is_null($filterQuery)) {
            $args['filterQuery'] = $filterQuery->getQuery();
        }

        $args = array_filter($args);

        $result = $this->convertResult($this->searchClient->search($args), $resultDocument);

        return $result;
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
        $facets       = $awsResult->getPath('facets');

        $result = new CloudSearchResult($amountOfHits, $start, $time, $facets);

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
}