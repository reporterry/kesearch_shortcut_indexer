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

/**
 * This class adds field from tt_content to the list of fields
 */
class ShortcutContentFields
{

    /** @var string field to fetch */
    private $column = 'records';

    /**
     *  Add the field to list
     *
     * @param string $fields
     * @param Page $pageIndexer
     */
    public function modifyPageContentFields(&$fields, $pageIndexer)
    {
        if ($this->getColumn()) {
            $fields .= "," . $this->getColumn();
        }
    }


    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @param string $column
     */
    public function setColumn(string $column): void
    {
        $this->column = $column;
    }
}
