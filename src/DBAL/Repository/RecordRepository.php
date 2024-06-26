<?php

namespace Aesislabs\Component\Odoo\DBAL\Repository;

use Aesislabs\Component\Odoo\DBAL\Expression\DomainInterface;
use Aesislabs\Component\Odoo\DBAL\Expression\ExpressionBuilder;
use Aesislabs\Component\Odoo\DBAL\Query\QueryBuilder;
use Aesislabs\Component\Odoo\DBAL\RecordManager;
use InvalidArgumentException;

class RecordRepository
{
    /**
     * @var RecordManager
     */
    private $recordManager;

    /**
     * @var string
     */
    private $modelName;

    public function __construct(RecordManager $recordManager, string $modelName)
    {
        $this->recordManager = $recordManager;
        $this->modelName = $modelName;
        $recordManager->addRepository($this);
    }

    /**
     * Insert a new record.
     *
     * @throws InvalidArgumentException when $data is empty
     *
     * @return int the ID of the new record
     */
    public function insert(array $data): int
    {
        if (!$data) {
            throw new InvalidArgumentException('Data cannot be empty');
        }

        return $this
            ->createQueryBuilder()
            ->insert()
            ->setValues($data)
            ->getQuery()
            ->execute();
    }

    /**
     * Update record(s).
     *
     * NB: It is not currently possible to perform “computed” updates
     * (where the value being set depends on an existing value of a record).
     *
     * @param array|int $ids
     */
    public function update($ids, array $data = []): void
    {
        if (!$data) {
            return;
        }

        $this
            ->createQueryBuilder()
            ->update((array) $ids)
            ->setValues($data)
            ->getQuery()
            ->execute();
    }

    /**
     * Delete record(s).
     *
     * @param array|int $ids
     */
    public function delete($ids): void
    {
        if (!$ids) {
            return;
        }

        $this
            ->createQueryBuilder()
            ->delete((array) $ids)
            ->getQuery()
            ->execute();
    }

    /**
     * Search one ID of record by criteria.
     */
    public function searchOne(?DomainInterface $criteria): ?int
    {
        return (int) $this
            ->createQueryBuilder()
            ->search()
            ->where($criteria)
            ->getQuery()
            ->getOneOrNullScalarResult();
    }

    /**
     * Search all ID of record(s).
     *
     * @return int[]
     */
    public function searchAll(array $orders = [], int $limit = null, int $offset = null): array
    {
        return $this->search(null, $orders, $limit, $offset);
    }

    /**
     * Search ID of record(s) by criteria.
     *
     * @return int[]
     */
    public function search(?DomainInterface $criteria = null, array $orders = [], int $limit = null, int $offset = null): array
    {
        /** @var int[] $result */
        $result = $this
            ->createQueryBuilder()
            ->search()
            ->where($criteria)
            ->setOrders($orders)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getScalarResult();

        return $result;
    }

    /**
     * Find ONE record by ID.
     *
     * @throws RecordNotFoundException when the record was not found
     */
    public function read(int $id, array $fields = []): array
    {
        $record = $this->find($id, $fields);

        if (!$record) {
            throw new RecordNotFoundException($this->modelName, $id);
        }

        return $record;
    }

    /**
     * Find ONE record by ID.
     */
    public function find(int $id, array $fields = []): ?array
    {
        return $this->findOneBy($this->expr()->eq('id', $id), $fields);
    }

    /**
     * Find ONE record by criteria.
     */
    public function findOneBy(?DomainInterface $criteria = null, array $fields = [], array $orders = [], int $offset = null): ?array
    {
        $result = $this->findBy($criteria, $fields, $orders, 1, $offset);

        return array_pop($result);
    }

    /**
     * Find all records.
     *
     * @return array[]
     */
    public function findAll(array $fields = [], array $orders = [], int $limit = null, int $offset = null): array
    {
        return $this->findBy(null, $fields, $orders, $limit, $offset);
    }

    /**
     * Find record(s) by criteria.
     *
     * @return array[]
     */
    public function findBy(?DomainInterface $criteria = null, array $fields = [], array $orders = [], int $limit = null, int $offset = null): array
    {
        return $this
            ->createQueryBuilder()
            ->select($fields)
            ->where($criteria)
            ->setOrders($orders)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Check if a record exists.
     */
    public function exists(int $id): bool
    {
        return 1 === $this->count($this->expr()->eq('id', $id));
    }

    /**
     * Count number of all records for the model.
     */
    public function countAll(): int
    {
        return $this->count();
    }

    /**
     * Count number of records for a model and criteria.
     */
    public function count(?DomainInterface $criteria = null): int
    {
        return $this
            ->createQueryBuilder()
            ->select()
            ->where($criteria)
            ->getQuery()
            ->count();
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->recordManager
            ->createQueryBuilder($this->modelName)
            ->select();
    }

    public function setRecordManager(RecordManager $recordManager): self
    {
        $this->recordManager = $recordManager;

        return $this;
    }

    public function getRecordManager(): RecordManager
    {
        return $this->recordManager;
    }

    public function getModelName(): string
    {
        return $this->modelName;
    }

    public function expr(): ExpressionBuilder
    {
        return $this->recordManager->getExpressionBuilder();
    }
}
