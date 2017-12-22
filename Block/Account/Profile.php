<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Block\Account;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\ResourceModel\Provider\CollectionFactory as ProviderCollectionFactory;
use Faonni\SocialLogin\Model\ResourceModel\Profile\CollectionFactory as ProfileCollectionFactory;

/**
 * Customer Account Profile Block
 */
class Profile extends Template
{
    /**
     * Route For Profile Delete Url
     */
    const ROUTE_ACCOUNT_DELETE_PROFILE = 'customer/account/deleteProfile';
    
    /**
     * Helper
     *
     * @var \Faonni\SocialLogin\Helper\Data
     */
    protected $_helper;
    
    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session;
     */
    protected $_session;
    
    /**
     * Providers Collection
     *
     * @var \Faonni\SocialLogin\Model\ResourceModel\Provider\Collection
     */
    protected $_providerCollection;

    /**
     * Providers Collection Factory
     *
     * @var \Faonni\SocialLogin\Model\ResourceModel\Provider\CollectionFactory
     */
    protected $_providerCollectionFactory;  
    
    /**
     * Profiles Collection
     *
     * @var \Faonni\SocialLogin\Model\ResourceModel\Profile\Collection
     */
    protected $_profileCollection;

    /**
     * Profiles Collection Factory
     *
     * @var \Faonni\SocialLogin\Model\ResourceModel\Profile\CollectionFactory
     */
    protected $_profileCollectionFactory;  
    
    /**
	 * Initialize Block
	 *
     * @param SocialLoginHelper $helper	 
     * @param Session $customerSession	 
     * @param ProviderCollectionFactory $providerCollectionFactory
     * @param ProfileCollectionFactory $profileCollectionFactory     
     * @param Context $context
     * @param array $data     
     */
    public function __construct(
        SocialLoginHelper $helper,    
        Session $customerSession,
        ProviderCollectionFactory $providerCollectionFactory,
        ProfileCollectionFactory $profileCollectionFactory,
        Context $context, 
        array $data = []
    ) {
        $this->_helper = $helper;       
        $this->_providerCollectionFactory = $providerCollectionFactory;
        $this->_profileCollectionFactory = $profileCollectionFactory;
        
        parent::__construct(
            $context, 
            $data
        );
        
        $this->_session = $customerSession;         
    } 
    
    /**
     * Preparing Global Layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $this->pageConfig->getTitle()->set(
			__('My Social Profiles')
		);
    }
    
    /**
     * Retrieve Profile Delete Url
     *
     * @return string
     */
    public function getDeleteUrl($profile)
    {
        return $this->getUrl(
			self::ROUTE_ACCOUNT_DELETE_PROFILE, 
			['id' => $profile->getId()]
		);
    }
    
    /**
     * Retrieve Provider Collection
     * 	     
     * @return \Faonni\SocialLogin\Model\ResourceModel\Provider\Collection
     */
    public function getProviderCollection()
    {
        if (null === $this->_providerCollection){
            $this->_providerCollection = $this->_providerCollectionFactory->create();
		}
		return $this->_providerCollection;
    }
    
    /**
     * Retrieve Provider Collection
     * 	     
     * @return \Faonni\SocialLogin\Model\ResourceModel\Profile\Collection
     */
    public function getProfileCollection()
    {
        if (null === $this->_profileCollection){
            $this->_profileCollection = $this->_profileCollectionFactory->create();
            $this->_profileCollection->addCustomerIdFilter(
                (int)$this->_session->getCustomerId()
            );
		}
		return $this->_profileCollection;
    }
    
	/**
	 * Check Popup mode	
	 * 
	 * @return bool
	 */	
 	public function isPopupMode()
	{
		return $this->_helper->isPopupMode();
	}     
}
