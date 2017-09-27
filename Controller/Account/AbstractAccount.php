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
use Magento\Customer\Controller\AbstractAccount as CustomerAbstractAccount;
use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\ProviderFactory;

/**
 * Abstract Account Controller
 */
abstract class AbstractAccount extends CustomerAbstractAccount
{
    /**
     * DataObject Factory
     *
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_dataObjectFactory;
    
    /**
     * Customer Session
	 *
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;  
    
    /**
     * Math Random
	 *
     * @var \Magento\Framework\Math\Random
     */
    protected $_mathRandom;    
    
    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;    
    
    /**
     * SocialLogin helper
     *
     * @var \Faonni\SocialLogin\Helper\Data
     */
    protected $_helper;
    
    /**
     * Provider model
     *
     * @var \Faonni\SocialLogin\Model\Provider
     */
    protected $_provider;    
    
    /**
     * Initialize controller
     *
     * @param Context $context
     * @param SocialLoginHelper $helper
     * @param ProviderFactory $providerFactory
     * @param DataObjectFactory $dataObjectFactory 
     * @param Session $customerSession
     * @param Random $mathRandom     
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SocialLoginHelper $helper,
        ProviderFactory $providerFactory,
        DataObjectFactory $dataObjectFactory,
        Session $customerSession,
        Random $mathRandom,
        LoggerInterface $logger
    ) {
        $this->_helper = $helper;
        $this->_provider = $providerFactory->create();
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_session = $customerSession;
        $this->_mathRandom = $mathRandom;
        $this->_logger = $logger;
        
        parent::__construct(
            $context
        );
    }
}
 
