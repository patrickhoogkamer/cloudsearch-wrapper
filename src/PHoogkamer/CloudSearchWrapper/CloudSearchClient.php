<?php namespace PHoogkamer\CloudSearchWrapper;

use Aws\CloudSearchDomain\CloudSearchDomainClient;

/**
 * Class CloudSearchClient
 */
class CloudSearchClient {

    /**
     * @var CloudSearchDomainClient
     */
    private $client;

    /**
     * Instantiate the private CloudSearchDomainClient.
     *
     * @param $endpoint
     * @param $key
     * @param $secret
     */
    public function __construct($endpoint, $key, $secret)
    {
        $this->client = CloudSearchDomainClient::factory([
            'base_url' => $endpoint,
            'key'      => $key,
            'secret'   => $secret
        ]);
    }

    /**
     * @param CloudSearchStructuredQuery $query
     * @param CloudSearchStructuredQuery $filterQuery
     * @param string $resultDocument
     * @return CloudSearchResult
     */
    public function search(CloudSearchStructuredQuery $query, CloudSearchStructuredQuery $filterQuery = null, $resultDocument = '\PHoogkamer\CloudSearchWrapper\CloudSearchDocument')
    {
        $args = [
            'queryParser' => 'structured',
            'query'       => $query->getQuery(),
            'start'       => $query->getStart(),
            'size'        => $query->getSize()
        ];

        $facet = $query->getFacet();

        if(!$query->facetIsEmpty())
        {
            $args['facet'] = $facet;
        }

        if(!is_null($filterQuery))
        {
            $args['filterQuery'] = $filterQuery->getQuery();
        }

        $args = array_filter($args);

        $result = $this->convertResult($this->client->search($args), $resultDocument);

        return $result;
    }

    /**
     * @param \Guzzle\Service\Resource\Model $awsResult
     * @param $resultDocument
     * @return CloudSearchResult
     * @throws \Exception
     */
    private function convertResult(\Guzzle\Service\Resource\Model $awsResult, $resultDocument)
    {
        $time         = $awsResult->getPath('status/timems');
        $amountOfHits = $awsResult->getPath('hits/found');
        $start        = $awsResult->getPath('hits/start');

        $result = new CloudSearchResult($amountOfHits, $start, $time);

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
        foreach ($documents as $document)
        {
            if(!($document instanceof CloudSearchDocument))
            {
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

        $this->client->uploadDocuments($args);
    }
}