<?php

namespace Goat\Core\Client;

use Goat\Core\Converter\ConverterMap;
use Goat\Core\Hydrator\HydratorMap;
use Goat\Core\Transaction\Transaction;
use Goat\Core\Query\SelectQuery;
use Goat\Core\Query\InsertValuesQuery;
use Goat\Core\Query\DeleteQuery;
use Goat\Core\Query\InsertQueryQuery;
use Goat\Core\Query\SqlFormatterInterface;
use Goat\Core\Query\UpdateQuery;

/**
 * Connection proxy that emits events via Symfony's EventDispatcher
 */
abstract class AbstractConnectionProxy implements ConnectionInterface
{
    /**
     * Get nested connection
     *
     * @return ConnectionInterface
     */
    abstract protected function getInnerConnection() : ConnectionInterface;

    /**
     * {@inheritdoc}
     */
    public function supportsReturning() : bool
    {
        return $this->getInnerConnection()->supportsReturning();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDeferingConstraints() : bool
    {
        return $this->getInnerConnection()->supportsDeferingConstraints();
    }

    /**
     * {@inheritdoc}
     */
    public function startTransaction(int $isolationLevel = Transaction::REPEATABLE_READ, bool $allowPending = false) : Transaction
    {
        return $this->getInnerConnection()->startTransaction($isolationLevel, $allowPending);
    }

    /**
     * {@inheritdoc}
     */
    public function isTransactionPending() : bool
    {
        return $this->getInnerConnection()->isTransactionPending();
    }

    /**
     * {@inheritdoc}
     */
    public function query($query, array $parameters = [], $options = null) : ResultIteratorInterface
    {
        return $this->getInnerConnection()->query($query, $parameters, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function perform($query, array $parameters = [], $options = null) : int
    {
        return $this->getInnerConnection()->perform($query, $parameters, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQuery($query, string $identifier = null) : string
    {
        return $this->getInnerConnection()->prepareQuery($query, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function executePreparedQuery(string $identifier, array $parameters = [], $options = null) : ResultIteratorInterface
    {
        return $this->getInnerConnection()->executePreparedQuery($identifier, $parameters, $options);
    }

    /**
     * {@inheritdoc}
     */
    // public function getLastInsertId()
    // {
    //     return $this->getInnerConnection()->getLastInsertId();
    // }

    /**
     * {@inheritdoc}
     */
    public function select(string $relationName, string $alias = null) : SelectQuery
    {
        return $this->getInnerConnection()->select($relationName, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function update(string $relationName, string $alias = null) : UpdateQuery
    {
        return $this->getInnerConnection()->update($relationName, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function insertValues(string $relationName) : InsertValuesQuery
    {
        return $this->getInnerConnection()->insertValues($relationName);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $relationName, string $alias = null) : DeleteQuery
    {
        return $this->getInnerConnection()->delete($relationName, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function insertQuery(string $relationName) : InsertQueryQuery
    {
        return $this->getInnerConnection()->insertQuery($relationName);
    }

    /**
     * {@inheritdoc}
     */
    public function truncateTables($relationNames)
    {
        return $this->getInnerConnection()->truncateTables($relationNames);
    }

    /**
     * {@inheritdoc}
     */
    public function setClientEncoding(string $encoding)
    {
        return $this->getInnerConnection()->setClientEncoding($encoding);
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlFormatter() : SqlFormatterInterface
    {
        return $this->getInnerConnection()->getSqlFormatter();
    }

    /**
     * {@inheritdoc}
     */
    public function getCastType(string $type) : string
    {
        return $this->getInnerConnection()->getCastType($type);
    }

    /**
     * {@inheritdoc}
     */
    public function setConverter(ConverterMap $converter)
    {
        return $this->getInnerConnection()->setConverter($converter);
    }

    /**
     * {@inheritdoc}
     */
    public function setHydratorMap(HydratorMap $hydratorMap)
    {
        return $this->getInnerConnection()->setHydratorMap($hydratorMap);
    }

    /**
     * {@inheritdoc}
     */
    public function escapeIdentifier(string $string) : string
    {
        return $this->getInnerConnection()->escapeIdentifier($string);
    }

    /**
     * {@inheritdoc}
     */
    public function escapeIdentifierList($strings) : string
    {
        return $this->getInnerConnection()->escapeIdentifierList($strings);
    }

    /**
     * {@inheritdoc}
     */
    public function escapeLiteral(string $string) : string
    {
        return $this->getInnerConnection()->escapeLiteral($string);
    }

    /**
     * {@inheritdoc}
     */
    public function escapeBlob(string $word) : string
    {
        return $this->getInnerConnection()->escapeBlob($word);
    }

    /**
     * {@inheritdoc}
     */
    public function isDebugEnabled() : bool
    {
        return $this->getInnerConnection()->isDebugEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function setDebug(bool $debug = true)
    {
        return $this->getInnerConnection()->setDebug($debug);
    }

    /**
     * {@inheritdoc}
     */
    public function debugMessage(string $message, int $level = E_USER_WARNING)
    {
        return $this->getInnerConnection()->debugMessage($message, $level);
    }

    /**
     * {@inheritdoc}
     */
    public function debugRaiseException(string $message = null, int $code = null, \Throwable $previous = null)
    {
        return $this->getInnerConnection()->debugRaiseException($message, $code, $previous);
    }
}
