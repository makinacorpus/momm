<?php

namespace Goat\Core\Query\Partial;

use Goat\Core\Client\ConnectionAwareInterface;
use Goat\Core\Client\ConnectionAwareTrait;
use Goat\Core\Error\GoatError;
use Goat\Core\Query\Query;

/**
 * Reprensents the basis of an SQL query.
 */
abstract class AbstractQuery implements Query, ConnectionAwareInterface
{
    use ConnectionAwareTrait;
    use AliasHolderTrait;

    private $relation;
    private $relationAlias;

    /**
     * Build a new query
     *
     * @param string $relation
     *   SQL from statement relation name
     * @param string $alias
     *   Alias for from clause relation
     */
    public function __construct($relation, $alias = null)
    {
        if (null === $alias) {
            $alias = $relation;
        }

        // Force our table to have a registered alias to avoid conflicts
        $alias = $this->getAliasFor($relation, $alias);

        $this->relation = $relation;
        $this->relationAlias = $alias;
    }

    /**
     * Get SQL from relation
     *
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Proxy of ::getAliasFor(::getRelation())
     *
     * @return string
     */
    final public function getRelationAlias()
    {
        return $this->relationAlias;
    }

    /**
     * {@inheritdoc}
     */
    final public function execute($class = Query::RET_PROXY, array $parameters = [])
    {
        if (!$this->connection) {
            throw new GoatError("this query has no reference to any connection, therefore cannot execute itself");
        }

        return $this->connection->query($this, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function perform(array $parameters = [])
    {
        if (!$this->connection) {
            throw new GoatError("this query has no reference to any connection, therefore cannot execute itself");
        }

        return $this->connection->perform($this, $parameters);
    }
}