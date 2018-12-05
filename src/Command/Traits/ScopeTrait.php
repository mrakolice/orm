<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\ORM\Command\Traits;

trait ScopeTrait
{
    /** @var array */
    private $scope = [];

    /** @var array */
    private $waitScope = [];

    /**
     * Wait for the context value.
     *
     * @param string $key
     */
    public function waitScope(string $key)
    {
        $this->waitScope[$key] = true;
    }

    /**
     * Set scope value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setScope(string $key, $value)
    {
        $this->scope[$key] = $value;
    }

    /**
     * @return array
     */
    public function getScope(): array
    {
        return $this->scope;
    }

    /**
     * Indicate that context value is not required anymore.
     *
     * @param string $key
     */
    protected function freeScope(string $key)
    {
        unset($this->waitScope[$key]);
    }
}