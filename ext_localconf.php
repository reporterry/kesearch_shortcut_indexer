<?php

defined('TYPO3_MODE') or die();

// encapsulate all locally defined variables
(function () {
    // Register hooks for indexing additional fields for shortcut CE.
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyPageContentFields'][] =
        \Hziegenhain\KesearchShortcutIndexer\ShortcutContentFields::class;
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyContentFromContentElement'][] =
        \Hziegenhain\KesearchShortcutIndexer\ShortcutContentFromContentElement::class;
})();
