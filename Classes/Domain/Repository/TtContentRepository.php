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
            ->orderBy('sorting')
            ;
        $result = $this->executeQueryBuilderFetchAll($queryBuilder, __METHOD__ . ':' . __LINE__);

        # $uids = [];
        # foreach ($result as $count => $data) {
        #    $uids[] = $data['uid'];
        # }
        # $this->logger->warning(var_export(['$pid' => $pid, '$uids' => $uids],true));

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
        
        $case = '0';
        if ($fieldValue) {
            $case = '1';
            if ($pdoType === \PDO::PARAM_NULL) {
                $case = '1.1';
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
                $case = '1.2';
                if ($pdoType !== null) {
                    $case = '1.2.1';
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
                else {
                    $case = '1.2.2';
                    $queryBuilder
                        ->select('*')
                        ->from($table)
                        ->where(
                            $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
                            $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                            $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                            $queryBuilder->expr()->eq($fieldName, $queryBuilder->createNamedParameter($fieldValue))
                        )
                        ;
                }
            }
            $result = $this->executeQueryBuilderFetchAll($queryBuilder, __METHOD__ . ':' . __LINE__);
        }
        else {
            $case = '2';
            $queryBuilder
                ->select($fieldName)
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->neq($fieldName, '""'),
                        $queryBuilder->expr()->isNotNull($fieldName)
                    )
                )
                ;

            # $result = [];
            $result = $this->executeQueryBuilderFetchAll($queryBuilder, __METHOD__ . ':' . __LINE__);
            # foreach ($tmpResult as $value) {
            #    $result[] = $value[$fieldName];
            # }
        }

        # ob_start();
        # var_dump([$case, func_get_args(), $result, $queryBuilder->getSQL(), $queryBuilder->getParameters()]);
        # $debug = ob_get_contents();
        # ob_end_clean();
        # $this->logger->warning($debug);

        return $result;
    }
    
    protected function getPdoType($fieldValue, $valueType)
    {
        $pdoType = null;
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
                        $pdoType = \PDO::PARAM_STR ;
                        break;
                    case 'string':
                        $pdoType = \PDO::PARAM_STR ;
                        break;
                    case 'null':
                        $pdoType = \PDO::PARAM_NULL;
                        break;
                    default:
                        $pdoType = \PDO::PARAM_STR ;
                        break;
                }
            }
            else {
                if (MathUtility::canBeInterpretedAsInteger($fieldValue)) {
                    $pdoType = \PDO::PARAM_INT;
                }
                else {
                    $pdoType = \PDO::PARAM_STR ;
                }
            }
        }
        return $pdoType;
    }
    // [ tt_content("tx_webcan_st_bt_element", 5, "int") ]
}
