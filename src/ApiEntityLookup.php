<?php

namespace PropertySuggester;

use LogicException;
use Http;
use Deserializers\Deserializer;
use Wikibase\DataModel\Services\Lookup\EntityLookup;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;

/**
 * Hacky class that loads entities using the wbgetentities API.
 *
 * @license GPL-2.0+
 * @author Marius Hoch
 */
class ApiEntityLookup implements EntityLookup {

	/**
	 * @var Deserializer
	 */
	private $itemDeserializer;

	/**
	 * @var string
	 */
	private $apiUrl;

	public function __construct( Deserializer $itemDeserializer, $apiUrl ) {
		$this->itemDeserializer = $itemDeserializer;
		$this->apiUrl = $apiUrl;
	}

	/**
	 * Returns the entity with the provided id.
	 *
	 * @note Implementations of this method may or may not resolve redirects.
	 * Code that needs control over redirect resolution should use an
	 * EntityRevisionLookup instead.
	 *
	 * @since 2.0
	 *
	 * @param EntityId $entityId
	 *
	 * @return EntityDocument|null
	 * @throws EntityLookupException
	 */
	public function getEntity( EntityId $entityId ) {
		$data = Http::get( $this->apiUrl . '?action=wbgetentities&format=json&ids=' . $entityId->getSerialization() );
		if ( !$data ) {
			return null;
		}

		$data = json_decode( $data, true );
		return $this->itemDeserializer->deserialize( $data['entities'][$entityId->getSerialization()] );
	}

	/**
	 * Returns whether the given entity can bee looked up using
	 * getEntity(). This avoids loading and deserializing entity content
	 * just to check whether the entity exists.
	 *
	 * @note Implementations of this method may or may not resolve redirects.
	 * Code that needs control over redirect resolution should use an
	 * EntityRevisionLookup instead.
	 *
	 * @since 1.1
	 *
	 * @param EntityId $entityId
	 *
	 * @return bool
	 * @throws EntityLookupException
	 */
	public function hasEntity( EntityId $entityId ) {
		throw new LogicException( 'Not implemented.' );
	}

}
