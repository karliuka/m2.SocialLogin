<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Math\Random;
use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\ProviderFactory;
use Faonni\SocialLogin\Model\ProfileFactory;
use Faonni\SocialLogin\Controller\Account\AbstractAccount;

/**
 * Delete Profile Controller
 */
class DeleteProfile extends AbstractAccount
{
    /**
     * SocialLogin profile
     *
     * @var \Faonni\SocialLogin\Model\Profile
     */
    protected $_profile; 
    
   /**
     * Initialize controller
     *
     * @param Context $context
     * @param SocialLoginHelper $helper
     * @param ProviderFactory $providerFactory
     * @param ProfileFactory $profile      
     * @param DataObjectFactory $dataObjectFactory 
     * @param Session $customerSession
     * @param Random $mathRandom     
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SocialLoginHelper $helper,
        ProviderFactory $providerFactory,
        ProfileFactory $profileFactory,
        DataObjectFactory $dataObjectFactory,
        Session $customerSession,
        Random $mathRandom,
        LoggerInterface $logger
    ) {
        $this->_profile = $profileFactory->create();
        
        parent::__construct(
            $context,
            $helper,
            $providerFactory,
            $dataObjectFactory,
            $customerSession,
            $mathRandom,
            $logger
        );
    }
    
    /**
     * Delete Action
     *
     * @throws \Magento\Framework\Exception\NotFoundException
     * @return \Magento\Framework\Controller\AbstractResult
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('id', false);
		if ($id) {
            try {
                $profile = $this->_profile->load($id);
				if ($profile->getCustomerId() === $this->_session->getCustomerId()) {
					$profile->delete();
					$this->messageManager->addSuccess(
						__('You deleted the profile.')
					);
				} else {
					$this->messageManager->addError(
						__('We can\'t delete the profile right now.')
					);
				}                
            } catch (\Exception $exception) {
                $this->messageManager->addError(
					$exception->getMessage()
				);
            }		
		} 
		return $this->resultRedirectFactory->create()
			->setPath('*/*/socialProfile');
    }
}