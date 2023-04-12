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
                }

                //Hook::add('Template::Settings::website', [$this, 'callbackShowWebsiteSettingsTabs']); NO

                // Intercept the LoadHandler hook to present
                // announcement pages when requested.
                Hook::add('LoadHandler', [$this, 'callbackHandleContent']);
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
        
        (new DAO())->retrieve( #https://php.watch/versions/8.0/non-static-static-call-fatal-error
                "SELECT type_id,GROUP_CONCAT(announcement_id) as announcement_id FROM announcements WHERE assoc_id = '$contextId' GROUP BY type_id"
        );
        
        
        $result_array = [];
        foreach ($result as $row) {
            $result_array[] =  json_decode(json_encode($row), true);
        }
        
        print_r($result_array);
        
        print_r($contextId);
    }


    /**
     * Declare the handler function to process the actual page PATH
     *
     * @param string $hookName The name of the invoked hook
     * @param array $args Hook parameters
     *
     * @return bool Hook handling status
     */
    public function callbackHandleContent($hookName, $args)
    {
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);

        $page = & $args[0];
        $op = & $args[1];
        $handler = & $args[3];

        /** @var StaticPagesDAO */
        $staticPagesDao = DAORegistry::getDAO('StaticPagesDAO');
        if ($page == 'pages' && $op == 'preview') {
            // This is a preview request; mock up a static page to display.
            // The handler class ensures that only managers and administrators
            // can do this.
            $staticPage = $staticPagesDao->newDataObject();
            $staticPage->setContent((array) $request->getUserVar('content'), null);
            $staticPage->setTitle((array) $request->getUserVar('title'), null);
        } else {
            // Construct a path to look for
            $path = $page;
            if ($op !== 'index') {
                $path .= "/${op}";
            }
            if ($ops = $request->getRequestedArgs()) {
                $path .= '/' . implode('/', $ops);
            }

            // Look for a static page with the given path
            $context = $request->getContext();
            $staticPage = $staticPagesDao->getByPath(
                $context ? $context->getId() : Application::CONTEXT_ID_NONE,
                $path
            );
        }

        // Check if this is a request for a static page or preview.
        if ($staticPage) {
            // Trick the handler into dealing with it normally
            $page = 'pages';
            $op = 'view';

            // It is -- attach the static pages handler.
            $handler = new StaticPagesHandler($this, $staticPage);
            return true;
        }
        return false;
    }

    //TO ELIMINATE, FOR ME

     /**
     * Permit requests to the announcement block grid handler
     *
     * @param string $hookName The name of the hook being invoked
     */
    
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



/*  CHUNK OF CODE NOT TO USE

public function setupGridHandler($hookName, $params) //see https://github.com/pkp/staticPages/blob/175fbddcb2e86933f90360e78f5472873748644a/StaticPagesPlugin.php
    {
        $component = & $params[0];
        $componentInstance = & $params[2];
        if ($component == 'plugins.generic.staticPages.controllers.grid.StaticPageGridHandler') {
            // Allow the static page grid handler to get the plugin object
            $componentInstance = new StaticPageGridHandler($this);
            return true;
        }
        return false;
    }

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
                'plugins.generic.announcementBlocks.controllers.grid.AnnouncementTypesHandler',
                'fetchGrid'
            )
        );
    }


*/