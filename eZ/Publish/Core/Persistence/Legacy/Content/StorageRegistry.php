<?php
/**
 * File containing the StorageRegistry class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Persistence\Legacy\Content;
use eZ\Publish\SPI\FieldType\FieldStorage,
    eZ\Publish\Core\Persistence\Legacy\Exception,
    eZ\Publish\Core\FieldType\NullStorage;

/**
 * Registry for external storages
 */
class StorageRegistry
{
    /**
     * Map of storages
     *
     * @var array
     */
    protected $storageMap = array();

    /**
     * Create field storage registry with converter map
     *
     * @param array $storageMap A map where key is field type key and value is a callable
     *                         factory to get FieldStorage OR FieldStorage instance
     */
    public function __construct( array $storageMap )
    {
        $this->storageMap = $storageMap;
    }

    /**
     * Returns the storage for $typeName
     *
     * @param string $typeName
     *
     * @throws \RuntimeException When type is neither FieldStorage instance or callable factory
     *
     * @return \eZ\Publish\SPI\FieldType\FieldStorage
     */
    public function getStorage( $typeName )
    {
        if ( !isset( $this->storageMap[$typeName] ) )
        {
            $this->storageMap[$typeName] = new NullStorage;
        }
        else if ( !$this->storageMap[$typeName] instanceof FieldStorage )
        {
            if ( !is_callable( $this->storageMap[$typeName] ) )
            {
                throw new \RuntimeException( "FieldStorage '$typeName' is neither callable or instance" );
            }

            $factory = $this->storageMap[$typeName];
            $this->storageMap[$typeName] = call_user_func( $factory );
        }
        return $this->storageMap[$typeName];
    }
}
