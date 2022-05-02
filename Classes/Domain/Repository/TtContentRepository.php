<?php

namespace WDB\WdbContentConditions\Domain\Repository;

use TYPO3\CMS\Core\Utility\MathUtility;

use Doctrine\DBAL\DriverManager;

class TtContentRepository extends AbstractRepository
{
    public function findByPid(int $pid)
    {
        $table = 'tt_content';
        $dbConn = DriverManager::getConnection($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']);
        $queryBuilder = $dbConn->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
            )
            ;
        $result = $this->executeQueryBuilderFetchAll($queryBuilder, __METHOD__ . ':' . __LINE__);
        return $result;
    }

    public function findByPidAndBtElement(int $pid, int $btElement = 5)
    {
        $table = 'tt_content';
        $dbConn = \Doctrine\DBAL\DriverManager::getConnection($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']);
        $queryBuilder = $dbConn->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('tx_webcan_st_bt_element', $queryBuilder->createNamedParameter($btElement, \PDO::PARAM_INT))
            )
            ;
        $result = $this->executeQueryBuilderFetchAll($queryBuilder, __METHOD__ . ':' . __LINE__);
        return $result;
    }

    public function findByPidAndFieldValue(int $pid, $fieldName, $fieldValue = '', $valueType = null)
    {
        $fieldName = trim($fieldName);
        if (!preg_match('/^[a-zA-Z0-9\_]+$/', $fieldName)) {
            throw new InvalidArgumentException('Invalid fieldname in condition: [' . $fieldName . ']', 1651409967);
        }
        $pdoType = $this->getPdoType($fieldValue, $valueType);
        $table = 'tt_content';
        $dbConn = \Doctrine\DBAL\DriverManager::getConnection($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']);
        $queryBuilder = $dbConn->createQueryBuilder();
        
        if ($fieldValue) {
            if ($pdoType === \PDO::PARAM_NULL) {
                $queryBuilder
                    ->select('*')
                    ->from($table)
                    ->where(
                        $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->isNull($fieldName)
                    )
                    ;
            } else {
                $queryBuilder
                    ->select('*')
                    ->from($table)
                    ->where(
                        $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq($fieldName, $queryBuilder->createNamedParameter($fieldValue, $pdoType))
                    )
                    ;
            }
        }
        else {
            $queryBuilder
                ->select('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->neq($fieldName, ''),
                        $queryBuilder->expr()->isNotNull($fieldName)
                    )
                )
                ;
        }
        $result = $this->executeQueryBuilderFetchAll($queryBuilder, __METHOD__ . ':' . __LINE__);
        return $result;
    }
    
    protected function getPdoType($fieldValue, $valueType)
    {
        if (strlen($fieldValue)) {
            if ($valueType !== null) {
                switch ($valueType) {
                    case 'int':
                    case 'integer':
                        $pdoType = \PDO::PARAM_INT;
                        break;
                    case 'bool':
                    case 'boolean':
                        $pdoType = \PDO::PARAM_BOOL;
                        break;
                    case 'double':
                    case 'float':
                        $pdoType = \PDO::PARAM_STRING;
                        break;
                    case 'string':
                        $pdoType = \PDO::PARAM_STRING;
                        break;
                    case 'null':
                        $pdoType = \PDO::PARAM_NULL;
                        break;
                    default:
                        $pdoType = \PDO::PARAM_STRING;
                        break;
                }
            }
            else {
                if (MathUtility::canBeInterpretedAsInteger($fieldValue)) {
                    $pdoType = \PDO::PARAM_INT;
                }
                else {
                    $pdoType = \PDO::PARAM_STRING;
                }
            }
        }
    }
    // [ tt_content("tx_webcan_st_bt_element", 5, "int") ]
}
