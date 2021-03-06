<?php

namespace Momm\Core\Client;

use Momm\Core\Converter\ConverterAwareInterface;

interface ConnectionInterface extends ConverterAwareInterface, EscaperInterface
{
    /**
     * Escape identifier (ie. table name, variable name, ...)
     *
     * @param string $string
     *
     * @return $string
     */
    public function escapeIdentifier($string);

    /**
     * Escape literal (string)
     *
     * @param string $string
     *
     * @return $string
     */
    public function escapeLiteral($string);

    /**
     * Escape blob
     *
     * @param string $string
     *
     * @return $string
     */
    public function escapeBlob($word);

    /**
     * Send query
     *
     * @param string $sql
     * @param mixed[] $parameters
     *   Query parameters
     * @param boolean $enableConverters
     *   If set to false, converters would be disabled
     *
     * @return ResultIteratorInterface
     */
    public function query($sql, array $parameters = [], $enableConverters = true);

    /**
     * Prepare query
     *
     * @param string $sql
     *   Bare SQL
     * @param string $identifier
     *   Query unique identifier, if null given one will be generated
     *
     * @return string
     *   The given or generated identifier
     */
    public function prepareQuery($sql, $identifier = null);

    /**
     * Prepare query
     *
     * @param string $identifier
     *   Query unique identifier
     * @param mixed[] $parameters
     *   Query parameters
     *
     * @return ResultIteratorInterface
     */
    public function executePreparedQuery($identifier, array $parameters = []);

    /**
     * {@inheritdoc}
     */
    public function setClientEncoding($encoding);
}
