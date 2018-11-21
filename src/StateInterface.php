<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\ORM;

/**
 * Resides above each of graph node and carry information about metadata, relations,
 * references and original state of data. Provides subscription mechanism to propagate
 * data changes across dependency tree.
 */
interface StateInterface
{
    public function onChange(callable $handler);

    public function setState(int $state): void;

    public function getState(): int;

    public function setData(array $data);

    public function getData(): array;

    public function getKey(string $key);

    // todo: check if i need it in this interface
    public function addReference();

    // todo: check if i need it in this interface
    public function decReference();

    // todo: check if i need it in this interface
    public function hasReferences(): bool;
}