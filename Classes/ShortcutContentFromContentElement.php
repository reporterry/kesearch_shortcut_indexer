<?php

declare(strict_types=1);

/**
 * This file is part of the "kesearch_shortcut_indexer" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Hziegenhain\KesearchShortcutIndexer;

use TeaminmediasPluswerk\KeSearch\Indexer\Types\Page;
use TeaminmediasPluswerk\KeSearch\Lib\Db;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use Zwo3\MaskKesearchIndexer\AdditionalContentFields;

/**
 * This class provides a possibility to index 'shortcut' elements in your indexer
 */
class ShortcutContentFromContentElement
{
    /**
     * @param string $bodytext
     * @param array $ttContentRow
     * @param Page $pageIndexer
     */
    public function modifyContentFromContentElement(string &$bodytext, array $ttContentRow, $pageIndexer)
    {
        $shortcutContentField = new ShortcutContentFields();
        $column = $shortcutContentField->getColumn();
        if (!empty($ttContentRow[$column]) && $ttContentRow['CType'] === 'shortcut') {
            // get related records
            $records = $this->resolveRecordList($ttContentRow[$column]);
            $bodytext .= $this->getContentFromRelatedRecords($records, $pageIndexer);

            // add the content to bodytext
            $bodytext .= strip_tags($ttContentRow[$column]);
        }
    }


    /**
     * @param string $recordsList
     * @return array
     */
    protected function resolveRecordList($recordsList = ''): array
    {
        $records = [];

        $relations = GeneralUtility::trimExplode(',', $recordsList);
        foreach ($relations as $val) {
            // Extract table name and id. [tablename]_[id]
            // where table name MIGHT contain "_", hence the reversion of the string!
            $val = strrev($val);
            $parts = explode('_', $val, 2);
            $theId = strrev($parts[0]);

            // Check that the id IS an integer:
            if (MathUtility::canBeInterpretedAsInteger($theId)) {
                // Get the table name: If a part of the exploded string, use that.
                $theTable = strrev(trim($parts[1]));
                $records[] = [
                    'table' => $theTable,
                    'uid' => $theId
                ];
            }
        }
        return $records;
    }

    /**
     * @param array $records
     * @return string
     */
    private function getContentFromRelatedRecords($records, $pageIndexer)
    {
        $bodytext = '';
        if (empty($records)) {
            return $bodytext;
        }

        foreach ($records as $record) {
            $queryBuilder = Db::getQueryBuilder($record['table']);
            $rows = $queryBuilder
                ->select(...['bodytext', 'CType'])
                ->from($record['table'])
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->quote($record['uid'], \PDO::PARAM_INT)
                    )
                )
                ->execute()->fetchAll();

            foreach ($rows as $row) {
                foreach ($row as $fieldname => $content) {
                    if (empty($content)) {
                        continue;
                    }

                    // spaces between field contents
                    if (!empty($bodytext)) {
                        $bodytext .= ' ';
                    }

                    // do not include CType value to index
                    if ($fieldname !== 'CType') {
                        $bodytext .= strip_tags($content);
                    }

                    // Instantiate EXT:mask_kesearch_indexer, if present, to allow indexing of EXT:mask contents
                    if (
                        ExtensionManagementUtility::isLoaded('mask_kesearch_indexer') &&
                        $fieldname === 'CType' && !stripos($content, 'mask_')
                    ) {
                        $rows = $queryBuilder
                            ->select('*')
                            ->from($record['table'])
                            ->where(
                                $queryBuilder->expr()->eq(
                                    'uid',
                                    $queryBuilder->quote($record['uid'], \PDO::PARAM_INT)
                                )
                            )
                            ->execute()->fetchAll();
                        $maskKesearchIndexer = GeneralUtility::makeInstance(AdditionalContentFields::class);
                        $bodytext .= $maskKesearchIndexer->modifyContentFromContentElement(
                            $bodytext,
                            $rows[0],
                            $pageIndexer
                        );
                    }
                }
            }
        }
        return $bodytext;
    }
}
