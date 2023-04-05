<?php

/**
* @file plugins/generic/announcementBlocks/controllers/grid/AnnouncementTypesGridHandler.php
 *
 * Copyright Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class AnnouncementTypesGridHandler
 * @ingroup controllers_grid_AnnouncementBlocks
 *
 * @brief Handle announcement types grid requests.
 */

 import('lib.pkp.pages.announcement.AnnouncementHandler');
 import('lib.pkp.classes.announcement.AnnouncementTypeDAO');

 class AnnouncementTypesGridHandler extends AnnouncementHandler
 {
    public function groupByType($args, $request)
    {
        //import('plugins.generic.AnnouncementBlocks.controllers.grid.form.AnnouncementBlockForm');
        if (!$request->getContext()->getData('enableAnnouncements')) {
            $request->getDispatcher()->handle404();
        }
        $group = array();
        foreach ($announcement as $announcements) {
            $id = $this->typeId();
            $group[$id][] = $this->getById($id);
            $templateMgr = TemplateManager::getManager();
            $templateMgr->assign('announcementType', $announcementType);
            return $announcementType;
            //$announcement_type = $announcement->typeId();
            //getById();
        }

    }
 }