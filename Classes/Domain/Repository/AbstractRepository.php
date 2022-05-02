<?php

declare(strict_types=1);

namespace WDB\WdbContentConditions\Domain\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Mysqli\MysqliException;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class AbstractRepository
{

    /**
     * Executes queryBuilder and returns resource object.
     * Displays any database errors.
     *
     * @param $queryBuilder
     * @param string $debugHeader
     *
     * @return null|object
     */
    protected function executeQueryBuilder($queryBuilder, $debugHeader): ?object
    {
        $resource = null;
        try {
            $resource = $queryBuilder->execute();
        } catch (DBALException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof MysqliException) {
                $errorCode = $previous->getCode();
                $errorMsg = $previous->getMessage();

                #DebuggerUtility::var_dump(['$queryBuilder' => $queryBuilder, '$errorCode' => $errorCode], __METHOD__.':'.__LINE__);
                var_dump([$debugHeader, '$errorCode' => $errorCode, '$errorMsg' => $errorMsg]);
            }
        }
        return $resource;
    }

    /**
     * Executes queryBuilder and returns result of fetchAll
     *
     * @param $queryBuilder
     * @param string $debugHeader
     *
     * @return null|array
     */
    protected function executeQueryBuilderFetchAll($queryBuilder, $debugHeader): ?array
    {
        $resource = $this->executeQueryBuilder($queryBuilder, $debugHeader);
        $result = [];
        if (is_object($resource)) {
            $result = $resource->fetchAll();
        }

        # var_dump([$debugHeader, 'resource' => $resource, '$result' => $result]);

        return $result;
    }

    /**
     * Executes queryBuilder and returns lastInsertId
     *
     * @param $queryBuilder
     * @param string $debugHeader
     *
     * @return null|int
     */
    protected function executeQueryBuilderInsert($queryBuilder, $debugHeader): ?int
    {
        $resource = $this->executeQueryBuilder($queryBuilder, $debugHeader);
        $lastInsertId = null;
        if (is_object($resource)) {
            $lastInsertId = $dbConn->lastInsertId();
        }

        # var_dump([$debugHeader, 'resource' => $resource, '$lastInsertId' => $lastInsertId]);

        return $lastInsertId;
    }
}
