<?php

/**
 * Snippet Manager Plugin
 * 
 * Bootstrap file to add a new snippet manager in order to support
 * more fall-through values.
 *
 * @author Niklas Buechner
 */
class Shopware_Plugins_Frontend_SnippetManager_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Snippet Manager';
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->subscribeEvent('Enlight_Controller_Front_StartDispatch', 'onInitLoader');
        
        $this->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Frontend_NBSnippetManager', 'onFrontendURLs');
        
        $this->subscribeEvent('Enlight_Bootstrap_InitResource_snippets', 'onSnippetService', 200);
        
        return true;
    }
    
    /**
     * Registers namespace
     */
    public function onInitLoader()
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\SnippetManager\Snippets',
            $this->Path() . 'Components/Snippets/'
        );
    }

    /**
     * Returns path to frontend testing controller
     * 
     * @return type
     */
    public function onFrontendURLs()
    {
        return $this->Path() . '/Controllers/Frontend/NBSnippetManager.php';
    }
    
    /**
     * Replace the global snippet service
     * 
     * @param Enlight_Event_EventArgs $args
     * @return \Shopware\SnippetManager\Snippets\SnippetManager
     */
    public function onSnippetService(Enlight_Event_EventArgs $args)
    {
        $injectContainer = $args->get('subject');
        
        $service = new \Shopware\SnippetManager\Snippets\SnippetManager(
                $injectContainer->get('models'),
                $injectContainer->getParameter('shopware.plugin_directories'),
                $injectContainer->getParameter('shopware.snippet')
            );
        
        return $service;
    }
}
