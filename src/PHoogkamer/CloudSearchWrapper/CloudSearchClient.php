<?php namespace PHoogkamer\CloudSearchWrapper;

use Aws\CloudSearchDomain\CloudSearchDomainClient;

/**
 * Class CloudSearchWrapper
 */
class CloudSearchClient {

	private $client;

	/**
	 * @param $endpoint
	 * @param $key
	 * @param $secret
	 */
	public function __construct($endpoint, $key, $secret)
	{
		$this->client = CloudSearchDomainClient::factory(array(
			'base_url'	=> $endpoint,
			'key'		=> $key,
			'secret'	=> $secret
		));
	}

	/**
	 * @param CloudSearchStructuredQuery $query
	 * @param CloudSearchStructuredQuery $filterQuery
	 * @return \Guzzle\Service\Resource\Model
	 */
	public function search(CloudSearchStructuredQuery $query, CloudSearchStructuredQuery $filterQuery = null)
	{
		$args = array(
			'queryParser' 	=> 'structured',
			'query' 		=> $query->getQuery(),
			'size'			=> $query->getSize()
		);

		if( ! is_null($filterQuery))
		{
			$args['filterQuery'] = $filterQuery->getQuery();
		}

		$args = array_filter($args);

		//TODO CloudSearchResult class
		return $this->client->search($args);
	}

	/**
	 * @param CloudSearchDocument $document
	 */
	public function pushDocument(CloudSearchDocument $document)
	{
		$this->uploadDocuments([$document->getDocument()]);
	}

	/**
	 * @param array $documents
	 * @throws \Exception
	 */
	public function pushDocuments(array $documents)
	{
		$arrayDocuments = [];

		/* @var $document CloudSearchDocument */
		foreach($documents as $document)
		{
			if( ! ($document instanceof CloudSearchDocument))
			{
				throw new \Exception('$documents must be an array of CloudSearchDocuments');
			}

			$arrayDocuments[] = $document->getDocument();
		}

		$this->uploadDocuments($arrayDocuments);
	}

	/**
	 * @param $documents
	 */
	private function uploadDocuments($documents)
	{
		$args = array(
			'contentType'	=> 'application/json',
			'documents' 	=> json_encode($documents)
		);

		$this->client->uploadDocuments($args);
	}
}