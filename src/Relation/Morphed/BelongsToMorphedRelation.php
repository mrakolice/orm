<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\ORM\Relation\Morphed;


use Spiral\ORM\Command\CarrierInterface;
use Spiral\ORM\Command\CommandInterface;
use Spiral\ORM\ORMInterface;
use Spiral\ORM\PromiseInterface;
use Spiral\ORM\Relation;
use Spiral\ORM\Relation\BelongsToRelation;
use Spiral\ORM\State;
use Spiral\ORM\Util\Promise;

class BelongsToMorphedRelation extends BelongsToRelation
{
    /** @var mixed|null */
    private $morphKey;

    /**
     * @param ORMInterface $orm
     * @param string       $class
     * @param string       $relation
     * @param array        $schema
     */
    public function __construct(ORMInterface $orm, string $class, string $relation, array $schema)
    {
        parent::__construct($orm, $class, $relation, $schema);
        $this->morphKey = $this->define(Relation::MORPH_KEY);
    }

    // todo: class
    public function initPromise(State $state, $data): array
    {
        if (empty($innerKey = $this->fetchKey($state, $this->innerKey))) {
            return [null, null];
        }

        // parent class (todo: i don't need it!!!!!!!! use aliases directly)
        $parentClass = $this->orm->getSchema()->getClass($this->fetchKey($state, $this->morphKey));

        if ($this->orm->getHeap()->hasPath("{$parentClass}:$innerKey")) {
            // todo: has it!
            $i = $this->orm->getHeap()->getPath("{$parentClass}:$innerKey");
            return [$i, $i];
        }

        // todo: i don't like carrying alias in a context (!!!!)
        // this is not right (!!)
        $pr = new Promise(
            [
                $this->outerKey => $innerKey,
                $this->morphKey => $this->fetchKey($state, $this->morphKey)
            ]
            , function ($context) use ($innerKey) {

            $parentClass = $this->orm->getSchema()->getClass($context[$this->morphKey]);

            if ($this->orm->getHeap()->hasPath("{$parentClass}:$innerKey")) {
                // todo: has it!
                $i = $this->orm->getHeap()->getPath("{$parentClass}:$innerKey");
                return $i;
            }

            // todo: optimize
            return $this->orm->getMapper($parentClass)->getRepository()->findOne([
                $this->outerKey => $context[$this->outerKey]
            ]);
        });

        return [$pr, $pr];
    }

    /**
     * @inheritdoc
     */
    public function queueRelation(
        CarrierInterface $parentCommand,
        $entity,
        State $state,
        $related,
        $original
    ): CommandInterface {
        $store = parent::queueRelation($parentCommand, $entity, $state, $related, $original);

        // todo: use forward as well

        if (is_null($related)) {
            if ($this->fetchKey($state, $this->morphKey) !== null) {
                $parentCommand->setContext($this->morphKey, null);
                $state->setData([$this->morphKey => null]);
            }
        } else {
            $relState = $this->getState($related);
            if ($this->fetchKey($state, $this->morphKey) != $relState->getAlias()) {
                $parentCommand->setContext($this->morphKey, $relState->getAlias());
                $state->setData([$this->morphKey => $relState->getAlias()]);
            }
        }

        return $store;
    }

    protected function getState($entity): ?State
    {
        if ($entity instanceof PromiseInterface) {
            $scope = $entity->__scope();

            return new State(
                State::PROMISED,
                [$this->outerKey => $scope[$this->outerKey]],
                $scope[$this->morphKey]
            );
        }

        return parent::getState($entity);
    }
}