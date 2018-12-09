<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\ORM;

use Spiral\ORM\Command\CommandInterface;
use Spiral\ORM\Command\ContextCarrierInterface;
use Spiral\ORM\Exception\MapperException;

/**
 * Provides basic capabilities for CRUD operations with given entity class (role).
 */
interface MapperInterface
{
    // points to the scope which must be applied to all queries
    public const SCOPE_DEFAULT = '@default';

    /**
     * Get role name mapper is responsible for.
     *
     * @return string
     */
    public function getRole(): string;

    /**
     * Get repository associated with a given mapper.
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Init empty entity object an return pre-filtered data (hydration will happen on a later stage). Must
     * return tuple [entity, entityData].
     *
     * @param array $data
     * @return array
     */
    public function init(array $data): array;

    /**
     * Hydrate entity with dataset.
     *
     * @param object $entity
     * @param array  $data
     * @return object
     *
     * @throws MapperException
     */
    public function hydrate($entity, array $data);

    /**
     * Extract all values from the entity.
     *
     * @param object $entity
     * @return array
     */
    public function extract($entity): array;

    /**
     * Initiate chain of commands require to store object and it's data into persistent storage.
     *
     * @param object $entity
     * @return ContextCarrierInterface
     *
     * @throws MapperException
     */
    public function queueStore($entity): ContextCarrierInterface;

    /**
     * Initiate sequence of of commands required to delete object from the persistent storage.
     *
     * @param object $entity
     * @return CommandInterface
     *
     * @throws MapperException
     */
    public function queueDelete($entity): CommandInterface;
}