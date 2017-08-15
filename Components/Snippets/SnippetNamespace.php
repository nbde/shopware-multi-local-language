<?php

namespace Shopware\SnippetManager\Snippets;

/**
 * Reads the snippets in the correct language they are available in
 *
 * @author Niklas Buechner
 */
class SnippetNamespace extends \Enlight_Components_Snippet_Namespace
{
    /**
     * Contains the locals to search snippets for
     * 
     * @var array
     */
    protected static $localsOrder;
    
    
    /**
     * Reads all snippets from the database and including the fall-through values
     * if the snippets are not available in the current language.
     */
    public function read()
    {
        if (!($this->_adapter instanceof \Shopware\Components\Snippet\DbAdapter))
        {
            parent::read();
            
            return;
        }
        
        $dbalConnection = Shopware()->Container()->get('dbal_connection');
        $entityManager = Shopware()->Container()->get('models');
        $snippetService = Shopware()->Container()->get('snippets');
        
        $shopId = $snippetService->getShopId();
        $localId = $snippetService->getLocale();
        $namespace = $this->getName();
        
        $data = [];
        
        if (!is_array(SnippetNamespace::$localsOrder))
        {
            $this->getLocalsInFallbackOrder($localId, $entityManager);
        }
        
        // Array is reversed so that the fall-through-values are first
        // in the sql result. Since the values at the beginning will
        // later be overriden in the foreach loop, the later and
        // important values will therefore be kept.
        $localsInOrder = array_reverse(SnippetNamespace::$localsOrder);
        
        $sql  = 'SELECT name, value FROM s_core_snippets WHERE namespace=\'';
        $sql .= $namespace . '\' AND shopID=';
        $sql .= $shopId . ' ORDER BY FIELD(localeID, ';
        $sql .= implode(', ', $localsInOrder);
        $sql .= ')';
        
        $snippets = $dbalConnection->query($sql)->fetchAll();
        
        foreach($snippets as $snippetEntry)
        {
            $data[$snippetEntry['name']] = $snippetEntry['value'];
        }
        
        $this->_data = $data;
    }
    
    /**
     * Populates the list of locals in the correct order
     * for fall-through values.
     * 
     * @param \Shopware\Models\Shop\Locale $mainStoreLocal
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    protected function getLocalsInFallbackOrder(
            \Shopware\Models\Shop\Locale $mainStoreLocal,
            \Doctrine\ORM\EntityManager $entityManager
    )
    {
        $locals = [];
        $locals[] = $mainStoreLocal->getId();
        $language = substr($mainStoreLocal->getLocale(), 0, 3);
        
        $localRepository = $entityManager->getRepository(\Shopware\Models\Shop\Locale::class);
        
        $query = $localRepository->createQueryBuilder('l')
                ->where('l.locale LIKE \'' . $language . '%\'')
                ->orderBy('l.id', 'ASC')
                ->getQuery();
        
        $foundLocals = $query->execute();
        
        foreach ($foundLocals as $locale)
        {
            $locals[] = $locale->getId();
        }
        
        $queryEnglish = $localRepository->createQueryBuilder('l')
                ->where('l.locale LIKE \'en_%\'')
                ->orderBy('l.id', 'ASC')
                ->getQuery();
        
        $foundEnglishLocals = $queryEnglish->execute();
        
        foreach ($foundEnglishLocals as $locale)
        {
            $locals[] = $locale->getId();
        }
        
        $locals = array_unique($locals);
        
        SnippetNamespace::$localsOrder = $locals;
    }
}
