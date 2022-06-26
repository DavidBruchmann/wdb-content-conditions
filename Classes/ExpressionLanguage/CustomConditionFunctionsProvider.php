<?php

namespace WDB\WdbContentConditions\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WDB\WdbContentConditions\Domain\Repository\TtContentRepository;

#use TYPO3\CMS\Core\Exception\MissingTsfeException;
#use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
#use TYPO3\CMS\Core\ExpressionLanguage\RequestWrapper;
#use TYPO3\CMS\Core\Site\Entity\Site;
#use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
#use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


class CustomConditionFunctionsProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            $this->getContentConditionsFunction(),
        ];
    }

    protected function getContentConditionsFunction(): ExpressionFunction
    {
        return new ExpressionFunction(
            'ttContent',
            static function () {
                // Not implemented, we only use the evaluator
            },
            static function ($arguments, $fieldName, $fieldValue = '', $valueType = '') {

                // `arguments` is an array with these keys:
                // ----------------------------------------
                // request            - TYPO3\CMS\Core\ExpressionLanguage\RequestWrapper
                // applicationContext - string
                // typo3              - stdClass
                // tree               - stdClass
                // frontend           - stdClass
                // backend            - stdClass
                // workspace          - stdClass
                // page               - array: page record

                if (!preg_match('/^[a-zA-Z0-9\_]+$/', $fieldName)) {
                    throw new InvalidArgumentException('Invalid fieldName as parameter: [' . $fieldName . ']', 1651409977);
                }
                if ($valueType) {
                    if (!in_array($valueType, [
                        'str','string',
                        'int','integer',
                        'bool','boolean',
                        'float','double',
                        'tstamp','timestamp',
                        'date',
                        'datetime',
                        'time',
                        '',
                        'null',
                        null,
                    ])) {
                        throw new InvalidArgumentException('Invalid valueType as parameter: [' . $valueType . ']', 1651409987);
                    }
                }
                $pid = $arguments['page']['uid'];
                $ttContentRepository = GeneralUtility::makeInstance(TtContentRepository::class);
                $pageContent = $ttContentRepository->findByPidAndFieldValue($pid, $fieldName, $fieldValue, $valueType);
                if (!$fieldValue) {
                    return $pageContent;
                } else {
                    return is_array($pageContent) && count($pageContent) > 0 ? true : false;
                }
            }
        );
    }
}
