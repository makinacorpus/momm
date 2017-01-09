<?php

namespace Goat\Core\Query;

use Goat\Core\Client\ArgumentBag;
use Goat\Core\Query\Partial\AbstractQuery;
use Goat\Core\Query\Partial\FromClauseTrait;
use Goat\Core\Query\Partial\ReturningClauseTrait;
use Goat\Core\Error\QueryError;

/**
 * Represents an UPDATE query
 */
class UpdateQuery extends AbstractQuery
{
    use FromClauseTrait;
    use ReturningClauseTrait;

    private $columns = [];
    private $relation;
    private $relationAlias;
    private $where;

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
        parent::__construct($relation, $alias);

        $this->where = new Where();
    }

    /**
     * Set a column value to update
     *
     * @param string $column
     *   Must be, as the SQL-92 standard states, a single column name without
     *   the table prefix or alias, it cannot be an expression
     * @param string|Statement $statement
     *   The column value, if it's a string it can be a reference to any other
     *   field from the table or the FROM clause, as well as it can be raw
     *   SQL
     *
     * @return $this
     */
    public function set($column, $statement)
    {
        if (!is_string($column) || false !== strpos($column, '.')) {
            throw new QueryError("column names in the set part of an update query can only be a column name, without table prefix");
        }
        if (!is_string($column) && !$column instanceof Statement) {
            throw new QueryError("column values must be valid SQL statements");
        }

        $this->columns[$column] = $statement;

        return $this;
    }

    /**
     * Set multiple column values to update
     *
     * @param string[]|Statement[] $values
     *   Keys are column names, as specified in the ::value() method, and values
     *   are statements as specified by the same method.
     *
     * @return $this
     */
    public function sets(array $values)
    {
        foreach ($values as $column => $statement) {
            $this->set($column, $statement);
        }

        return $this;
    }

    /**
     * Get all updated columns
     *
     * @return string[]|Statement[]
     *   Keys are column names, values are either strings or Statement instances
     */
    public function getUpdatedColumns()
    {
        return $this->columns;
    }

    /**
     * Add a condition in the where clause
     *
     * @param string $column
     * @param mixed $value
     * @param string $operator
     *
     * @return $this
     */
    public function condition($column, $value, $operator = Where::EQUAL)
    {
        $this->where->condition($column, $value, $operator);

        return $this;
    }

    /**
     * Add an abitrary statement to the where clause
     *
     * @param string $statement
     *   SQL string, which may contain parameters
     * @param mixed[] $arguments
     *   Parameters for the arbitrary SQL
     *
     * @return $this
     */
    public function statement($statement, $arguments = [])
    {
        $this->where->statement($statement, $arguments);

        return $this;
    }

    /**
     * Get where statement
     *
     * @return Where
     */
    public function where()
    {
        return $this->where;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        $arguments = new ArgumentBag();

        foreach ($this->columns as $statement) {
            if ($statement instanceof Statement) {
                $arguments->append($statement->getArguments());
            } else {
                $arguments->add($statement);
            }
        }

        foreach ($this->joins as $join) {
            $arguments->append($join[1]->getArguments());
        }

        if (!$this->where->isEmpty()) {
            $arguments->append($this->where->getArguments());
        }

        return $arguments;
    }

    /**
     * Deep clone support.
     */
    public function __clone()
    {
        $this->cloneJoins();

        foreach ($this->columns as $column => $statement) {
            if (is_object($statement)) {
                $this->columns[$column] = clone $statement;
            }
        }

        $this->where = clone $this->where;
    }
}