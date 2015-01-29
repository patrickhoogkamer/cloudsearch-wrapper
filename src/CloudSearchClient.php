<?php

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
	 * @param CloudSearchQuery $query
	 */
	public function search(CloudSearchQuery $query)
	{

	}

	public function push(CloudSearchDocument $document)
	{

	}
}