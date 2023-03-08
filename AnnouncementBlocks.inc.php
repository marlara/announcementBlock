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

import('lib.pkp.classes.announcement.AnnouncementType');
import('lib.pkp.classes.plugins.GenericPlugin');

class AnnouncementBlocksPlugin extends GenericPlugin {
    /**
	 * @copydoc GenericPlugin::register()
	 */
	public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled($mainContextId)) { //see https://github.com/pkp/customBlockManager/blob/main/CustomBlockManagerPlugin.inc.php
            // If the system isn't installed, or is performing an upgrade, don't
            // register hooks. This will prevent DB access attempts before the
            // schema is installed.
			if (Application::isUnderMaintenance()) {
                return true;
            }

            if ($this->getEnabled($mainContextId)) {
                $this->import('AnnouncementBlocksPlugin');

                // Ensure that there is a context (journal or press)
                if ($request = Application::get()->getRequest()) {
                    if ($mainContextId) {
                        $contextId = $mainContextId;
                    } else {
                        $context = $request->getContext();
                        $contextId = $context ? $context->getId() : \PKP\core\PKPApplication::CONTEXT_SITE;
                    }

                    // Load the announcement blocks we have created 
                    $announcements = $this->getSetting($contextId, 'announcements'); //TO BE MODIFIED!
                    if (!is_array($announcements)) {
                        $announcements = [];
                    }

                    // Loop through each announcement block and register it 
                    $i = 0;
                    foreach ($announcements as $announcement) {
                        PluginRegistry::register(
                            'announcements',
                            new AnnouncementBlocksPlugin($announcement, $this), 
                            $this->getPluginPath() //TO BE MODIFIED!
                        );
                    }
                }

                // This hook is used to register the components this plugin implements to
                // permit administration of custom block plugins.
                Hook::add('LoadComponentHandler', [$this, 'setupGridHandler']);
            }
            return true;
        }
        return false;
    }

    //see also https://github.com/pkp/pkp-lib/blob/main/pages/announcement/AnnouncementHandler.php

    

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

