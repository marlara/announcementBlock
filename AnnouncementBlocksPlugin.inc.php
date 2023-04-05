<?php

/**
* @file plugins/generic/announcementBlocks/AnnouncementBlocksPlugin.inc.php
 *
 * Copyright Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class AnnouncementBlocksPlugin
 * @ingroup plugins_generic_AnnouncementBlocks
 *
 * @brief Manage the Announcement Types.
 */

import('classes.core.Application');
//import('lib.pkp.classes.facades.Repo');
import('lib.pkp.classes.plugins.GenericPlugin');

class AnnouncementBlocksPlugin extends GenericPlugin {
    /**
	 * @copydoc GenericPlugin::register()
	 */
	public function register($category, $path, $mainContextId = NULL) {
        $success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled($mainContextId)){
                $this->import('AnnouncementBlocksPlugin');

                // Ensure that there is a context (journal or press)
                if ($request = Application::get()->getRequest()) {
                    if ($mainContextId) {
                        $contextId = $mainContextId;
                    } else {
                        $context = $request->getContext();
                        $contextId = $context ? $context->getId() : \PKP\core\PKPApplication::CONTEXT_SITE;
                    }

                    
                    // Load the announcements
                    //$announcements = $this->getSetting($contextId, 'announcements'); //TO BE MODIFIED!
                    //if (!is_array($announcements)) {
                    //    $announcements = [];
                    //}

                    // Loop through each announcements and get associated type 
                    //$i = 0;
                    //foreach ($announcements as $announcement) {
                    //    $announcementType = $this->getAssocType();
                    //}
                }

                // This hook is used to register the components this plugin implements from the already existing AnnouncementHandler
                HookRegistry::register('LoadComponentHandler', [$this, 'setupGridHandler']);
                HookRegistry::register('TemplateManager::setupBackendPage', [$this, 'trySomething']);
            }
            return $success;
        }
    

    //see also https://github.com/pkp/pkp-lib/blob/main/pages/announcement/AnnouncementHandler.php
    //and https://github.com/pkp/customBlockManager/blob/main/controllers/grid/form/CustomBlockForm.inc.php
    //and https://github.com/pkp/pkp-lib/blob/efb124de36758eaece88ad911326302635198d03/controllers/grid/announcements/form/AnnouncementTypeForm.php
    

    public function trySomething($hookName, $param)
    {
        import('lib.pkp.pages.announcement.AnnouncementHandler');
        import('lib.pkp.classes.announcement.AnnouncementTypeDAO');

        $request = Application::get()->getRequest();
        $context = $request->getContext(); #https://docs.pkp.sfu.ca/dev/documentation/en/architecture
        $contextId = $context ? $context->getId() : \PKP\core\PKPApplication::CONTEXT_SITE;
        
        $announcementTypeDao = DAORegistry::getDAO('AnnouncementTypeDAO');

        $result = (new DAO())->retrieve( #https://php.watch/versions/8.0/non-static-static-call-fatal-error
                "SELECT type_id,GROUP_CONCAT(announcement_id) FROM announcements WHERE assoc_id = '$contextId' GROUP BY type_id"
            );
        
        $result_array = [];
        foreach ($result as $row) {
            $result_array[] = $row;
        }
        
        print_r($result_array);
        
        print_r($contextId);
    }
    
     /**
     * Permit requests to the announcement block grid handler
     *
     * @param string $hookName The name of the hook being invoked
     */
    public function setupGridHandler($hookName, $params)
    
    {
        $component = & $params[0];
        if ($component == 'plugins.generic.announcementBlocks.controllers.grid.AnnouncementTypesGridHandler') {
            define('ACCOUNCEMENTBLOCKS_PLUGIN_NAME', $this->getName());
            return true;
        }
        return false;
    }

    /**
     * @copydoc Plugin::manage()
     */
    public function manage($args, $request)
    {
        $templateMgr = TemplateManager::getManager($request);
        $dispatcher = $request->getDispatcher();
        return $templateMgr->fetchAjax(
            'announcementBlocksGridUrlGridContainer',
            $dispatcher->url(
                $request,
                Application::ROUTE_COMPONENT,
                null,
                'plugins.generic.announcementBlocks.controllers.grid.AnnouncementTypesGridHandler',
                'fetchGrid'
            )
        );
    }
    
    /**
     * Provide a name for this plugin
     *
     * The name will appear in the plugins list where editors can
     * enable and disable plugins.
     * 
     * @return string
     */
    public function getDisplayName() {
        return __('plugins.generic.announcementBlocks.displayName');
    }

    /**
    * Provide a description for this plugin
    *
    * The description will appear in the plugins list where editors can
    * enable and disable plugins.
    *
    * @return string
    */
    public function getDescription() {
        return __('plugins.generic.announcementBlocks.description');
    }



}

