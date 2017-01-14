<?php

namespace Goat\Driver\PDO;

use Goat\Core\Transaction\Transaction;
use Goat\Core\Query\SqlFormatterInterface;

class MySQLConnection extends AbstractPDOConnection
{
    /**
     * {@inheritdoc}
     */
    public function supportsReturning() : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDeferingConstraints() : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doStartTransaction(int $isolationLevel = Transaction::REPEATABLE_READ) : Transaction
    {
        $ret = new MySQLTransaction($isolationLevel);
        $ret->setConnection($this);

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getCastType(string $type) : string
    {
        // Specific type conversion for MySQL because its CAST() function
        // does not accepts the same datatypes as the one it handles.
        if ('timestamp' === $type) {
            return 'datetime';
        } else if ('int' === substr($type, 0, 3)) {
            return 'signed integer';
        } else if ('float' === substr($type, 0, 5) || 'double' === substr($type, 0, 6)) {
            return 'decimal';
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function escapeIdentifier(string $string) : string
    {
        return '`' . str_replace('`', '\\`', $string) . '`';
    }

    /**
     * {@inheritdoc}
     */
    public function setClientEncoding(string $encoding)
    {
        // Keeping the MySQL-specific client encoding directive to ensure it
        // will work with older MySQL versions. It seems while browsing
        // randomly the documentation that versions prior to 5.5 don't support
        // this, or it's undocumented.
        // https://dev.mysql.com/doc/refman/5.7/en/set-names.html
        $this
            ->getPdo()
            ->query(
                sprintf(
                    "SET character_set_client = %s",
                    $this->escapeLiteral($encoding)
                )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function createFormatter() : SqlFormatterInterface
    {
        return new MySQLFormatter($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function sendConfiguration(array $configuration)
    {
        $pdo = $this->getPdo();

        foreach ($configuration as $key => $value) {
            $pdo->query(sprintf(
                "SET %s = %s",
                $this->escapeIdentifier($key),
                $this->escapeLiteral($value)
            ));
        }

        return $this;
    }
}
