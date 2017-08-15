<?php

namespace Shopware\SnippetManager\Snippets;

use \Shopware_Components_Snippet_Manager;

/**
 * Replacement for global snippet service. Extends original service
 * in order to keep compatibility.
 *
 * @author Niklas Buechner
 */
class SnippetManager extends Shopware_Components_Snippet_Manager
{
    /**
     * 
     * Inits the service while changing the class for data loading
     * 
     * @param \Shopware\SnippetManager\Snippets\ModelManager $modelManager
     * @param array $pluginDirectories
     * @param array $snippetConfig
     */
    public function __construct(ModelManager $modelManager, array $pluginDirectories, array $snippetConfig)
    {
        parent::__construct($modelManager, $pluginDirectories, $snippetConfig);
        
        $this->defaultNamespaceClass = '\Shopware\SnippetManager\Snippets\SnippetNamespace';
    }
    
    /**
     * Returns the current locale.
     * 
     * @return \Shopware\Models\Shop\Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Returns the shop id;
     * 
     * @return integer
     */
    public function getShopId()
    {
        if ($this->shop instanceof \Shopware\Models\Shop\DetachedShop)
        {
            return $this->shop->getId();
        }
        else
        {
            // Backend :)
            
            return 1;
        }
    }
}
