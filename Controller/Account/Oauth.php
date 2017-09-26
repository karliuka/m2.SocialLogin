<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Controller\Account;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\AbstractResult;
use Faonni\SocialLogin\Controller\Account\AbstractAccount;

/**
 * Oauth Controller
 */
class Oauth extends AbstractAccount
{
    /**
     * Oauth action
     *
     * @throws \Magento\Framework\Exception\NotFoundException
     * @return \Magento\Framework\Controller\AbstractResult
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('id', false);
		$code = $this->getRequest()->getParam('code', false);
		$state = $this->getRequest()->getParam('state', false);
		
        if ($this->_helper->isEnabled() && $id && $code && $state) {
            try {        
                $provider = $this->_provider->load($id);
                if ($provider) {
                    if ($provider->obtainToken($code)) {
                        $provider->setRequestState($state);
                        /** @var \Magento\Framework\DataObject $response */
                        $response = $this->_dataObjectFactory->create();
                        $this->_eventManager->dispatch(
                            'faonni_sociallogin_obtain_token_after', 
                            ['provider' => $provider, 'response' => $response]
                        );                    
                        $result = $response->getResult();
                        if ($result instanceof AbstractResult) {
                            return $result;                 
                        } elseif ($result) {
                            $this->_logger->addError(__('Not a valid %1 Provider Result', $id));
                        } 
                    } else {
                        $this->_logger->addError(__('Error Obtain Token the %1 Provider', $id));
                    }
                } else {
                    $this->_logger->addError(__('Error Loading the %1 Provider', $id));
                }
            } catch (Exception $e) {
                $this->_logger->critical($e);
            }             
        }
        throw new NotFoundException(__('Page not found.'));
    }
}