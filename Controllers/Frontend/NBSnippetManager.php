<?php

/**
 * Testing class for the new snippet manager.
 *
 * @author Niklas Buechner
 */
class Shopware_Controllers_Frontend_NBSnippetManager extends Enlight_Controller_Action
{
    /**
     * Actions to test loading an Austrian value
     */
    public function indexAction()
    {
        
        $entityManager = $this->get('models');
        $localsRepository = $entityManager->getRepository(\Shopware\Models\Shop\Locale::class);
        $deAt = $localsRepository->findOneBy(['locale' => 'de_AT']);
        
        $snippetManager = $this->get('snippets');
        $snippetManager->setLocale($deAt);
        $namespace = $snippetManager->getNamespace('frontend/plugins/payment/sepa');
        
        echo $namespace->get('PaymentSepaLabelUseBillingData') . '<br />';
        echo $namespace->get('ErrorIBAN');
        
        die();
    }
}
