<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Controller\Account;

use Magento\Framework\Exception\NotFoundException;
use Faonni\SocialLogin\Model\Provider;
use Faonni\SocialLogin\Controller\Account\AbstractAccount;

/**
 * Init Provider Controller
 */
class InitProvider extends AbstractAccount
{
    /**
     * Init Provider
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $display = $this->getRequest()->getParam('display');
        
        if ($this->_helper->isEnabled() && $id) {
            try {
                $provider = $this->_provider->load($id);
                
                $salt = $this->_mathRandom->getRandomString(32);
                $this->_session->setSocialLoginSalt($salt);
                $this->_session->setSocialLoginDisplay($display);
                
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();                                
                $resultRedirect->setUrl(
                    $provider->getProviderUrl(Provider::SCOPE_PREFIX, $salt)
                );
                return $resultRedirect;                  
            } 
            catch (Exception $e) {
                $this->_logger->addError(__('Error Loading the %1 Provider', $id));
            }             
        }
        throw new NotFoundException(__('Page not found.'));
    }
}
