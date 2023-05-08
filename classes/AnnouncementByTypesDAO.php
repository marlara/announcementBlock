<?php

/**
* @file plugins/generic/announcementBlocks/classes/AnnouncementByTypesDAO.php
 *
 * Copyright Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class AnnouncementTypedHandler
 * @ingroup pages_announcement
 *
 * @brief Handle requests for public announcement types functions.
 */

 import('lib.pkp.pages.announcement.AnnouncementHandler');
 import('lib.pkp.classes.announcement.AnnouncementTypeDAO');

 //see https://github.com/pkp/pkp-lib/blob/a351645bf600be7e1c2ac705c6f6f66ced8e4b3c/api/v1/announcements/PKPAnnouncementHandler.php

 class AnnouncementByTypesDAO extends AnnouncementTypeDAO
 {
    public function groupByType($contextId)
    {
        //$request = Application::get()->getRequest();
        //$context = $request->getContext(); #https://docs.pkp.sfu.ca/dev/documentation/en/architecture
        //$contextId = $context ? $context->getId() : \PKP\core\PKPApplication::CONTEXT_SITE;

        $result = (new DAO())->retrieve( #https://php.watch/versions/8.0/non-static-static-call-fatal-error
                "SELECT type_id,GROUP_CONCAT(announcement_id) as announcement_id FROM announcements WHERE assoc_id = '$contextId' GROUP BY type_id"
        );
        
        
        $result_array = [];
        foreach ($result as $row) {
            $result_array[] =  json_decode(json_encode($row), true);
        }
        
        return $result_array; //returns an array of arrays with type_id and announcement_id as keys. Ex: Array ( [0] => Array ( [type_id] => 1 [announcement_id] => 2,3 ) [1] => Array ( [type_id] => 2 [announcement_id] => 1 ) )

    }

    public function setPath($typeId, $contextId=null)
    {
        $params = [(int) $typeId];
        if ($contextId !== null) {
            $params[] = (int) $contextId;
        }
    }
 }