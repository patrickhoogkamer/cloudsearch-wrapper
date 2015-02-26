<?php namespace PHoogkamer\CloudSearchWrapper;

/**
 * Interface CloudSearchDocumentInterface
 *
 * @package PHoogkamer\CloudSearchWrapper
 */
interface CloudSearchDocumentInterface {

    /**
     * Implement when object needs to be filled with a $hit AWS object
     *
     * @param array $hit
     */
    public function fillWithHit(array $hit);
}