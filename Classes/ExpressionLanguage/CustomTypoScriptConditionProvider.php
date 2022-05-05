<?php

namespace WDB\WdbContentConditions\ExpressionLanguage;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WDB\WdbContentConditions\Domain\Repository\TtContentRepository;

class CustomTypoScriptConditionProvider extends AbstractProvider
{
    public function __construct(int $pageId = null)
    {
        // $this->context = $context ?? GeneralUtility::makeInstance(Context::class);
        $this->pageId = $pageId ?? $this->determinePageId();
        /*
        $pid = $GLOBALS['TSFE']->id;
        */
        $ttContentRepository = GeneralUtility::makeInstance(TtContentRepository::class);
        $result = $ttContentRepository->findByPid($this->pageId);
        $tt_content = [];
        // $uids = [];
        foreach ($result as $key => $value) {
            $tt_content[$value['uid']] = $value;
            // $uids[] = $value['uid'];
        }
        $this->expressionLanguageVariables = [
            'tt_content' => $tt_content,
        ];
        $this->expressionLanguageProviders = [
            CustomConditionFunctionsProvider::class,
        ];
    }

    /**
     * Tries to determine the ID of the page currently processed.
     * When User/Group TS-Config is parsed when no specific page is handled
     * (i.e. in the Extension Manager, etc.) this function will return "0", so that
     * the accordant conditions (e.g. PIDinRootline) will return "FALSE"
     *
     * @return int The determined page id or otherwise 0
     */
    private function determinePageId(): int
    {
        $pageId = 0;
        // Determine id from module that was called with an id:
        if (isset($GLOBALS['TSFE']) && !empty($GLOBALS['TSFE']->id)) {
            $pageId = $GLOBALS['TSFE']->id;
        }
        elseif ($id = (int)GeneralUtility::_GP('id')) {
            $pageId = $id;
        } else {
            $editStatement = GeneralUtility::_GP('edit');
            $commandStatement = GeneralUtility::_GP('cmd');
            if (is_array($editStatement)) {
                $table = key($editStatement);
                $uidAndAction = current($editStatement);
                $uid = (int)key($uidAndAction);
                $action = current($uidAndAction);
                if ($action === 'edit') {
                    $pageId = $this->getPageIdByRecord($table, $uid);
                } elseif ($action === 'new') {
                    $pageId = $this->getPageIdByRecord($table, $uid, true);
                }
            } elseif (is_array($commandStatement)) {
                $table = key($commandStatement);
                $uidActionAndTarget = current($commandStatement);
                $uid = (int)key($uidActionAndTarget);
                $actionAndTarget = current($uidActionAndTarget);
                $action = key($actionAndTarget);
                $target = current($actionAndTarget);
                if ($action === 'delete') {
                    $pageId = $this->getPageIdByRecord($table, $uid);
                } elseif ($action === 'copy' || $action === 'move') {
                    $pageId = $this->getPageIdByRecord($table, (int)($target['target'] ?? $target), true);
                }
            }
        }
        return $pageId;
    }

    /**
     * Gets the page id by a record.
     *
     * @param string $table Name of the table
     * @param int $id Id of the accordant record
     * @param bool $ignoreTable Whether to ignore the page, if TRUE a positive
     * @return int Id of the page the record is persisted on
     */
    private function getPageIdByRecord(string $table, int $id, bool $ignoreTable = false): int
    {
        $pageId = 0;
        if ($table && $id) {
            if (($ignoreTable || $table === 'pages') && $id >= 0) {
                $pageId = $id;
            } else {
                $record = BackendUtility::getRecordWSOL($table, abs($id), '*', '', false);
                $pageId = (int)$record['pid'];
            }
        }
        return $pageId;
    }
}
