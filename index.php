<?php
/**
 * @defgroup plugins_generic_announcementBlocks
 */
/**
 * @file plugins/generic/announcementBlocks/index.php
 *
 * Copyright (c) Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @ingroup plugins_generic_announcementBlocks
 * @brief Wrapper for the AnnouncementBlocks plugin.
 *
 */

require_once('AnnouncementBlocksPlugin.inc.php');
return new AnnouncementBlocksPlugin();
