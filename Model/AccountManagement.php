<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Math\Random;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\Data\CustomerInterfaceFactory as CustomerFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\Store;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\Profile\Data as ProfileData;
use Faonni\SocialLogin\Model\Profile;

/**
 * Handle various customer account actions
 */
class AccountManagement
{
    /**
     * Account Management
	 *
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $_accountManagement;
	
    /**
     * The Redirect URL
	 *
     * @var string
     */
	protected $_popupCloseUrl = 'customer/account/PopupClose';
	
    /**
     * The Account URL
	 *
     * @var string
     */
	protected $_accountUrl = 'customer/account';
	
    /**
     * New account flag
     *
     * @var bool
     */
    protected $_isNewAccount;	
	
    /**
     * Store instance
	 *
     * @var \Magento\Store\Model\Store
     */
	protected $_store;
	
    /**
     * SocialLogin helper
     *
     * @var \Faonni\SocialLogin\Helper\Data
     */
    protected $_helper; 
	
    /**	
     * Customer Authentication
	 *	
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    protected $_authentication;	
	
    /**
     * Event Manager
	 *	
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;	
	
    /**
     * Customer Session
	 *
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;
	
    /**
     * Customer Factory
	 *
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $_customerFactory;
	
    /**
     * Customer Repository
	 *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;	
	
    /**
     * Cookie Metadata Factory
	 *
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    /**
     * Cookie Manager
	 *
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $_cookieManager;
	
    /**
     * DataObject Helper
	 *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * Math Random
	 *
     * @var \Magento\Framework\Math\Random
     */
    protected $_mathRandom;
    
    /**
     * Initialize model
     *  
     * @param AccountManagementInterface $accountManagement	 
     * @param ManagerInterface $eventManager	 
     * @param Session $customerSession  
     * @param CustomerFactory $customerFactory	 
     * @param CustomerRepositoryInterface $customerRepository
     * @param AuthenticationInterface $authentication
     * @param PhpCookieManager $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory	
     * @param DataObjectHelper $dataObjectHelper
     * @param SocialLoginHelper $helper
     * @param Random $mathRandom	 
     */
    public function __construct(
        AccountManagementInterface $accountManagement,
		ManagerInterface $eventManager,
		Session $customerSession,
		CustomerFactory $customerFactory,
		CustomerRepositoryInterface $customerRepository,
		AuthenticationInterface $authentication,
		PhpCookieManager $cookieManager,
		CookieMetadataFactory $cookieMetadataFactory,
		DataObjectHelper $dataObjectHelper,
		SocialLoginHelper $helper,
		Random $mathRandom
    ) {
        $this->_accountManagement = $accountManagement;
		$this->_eventManager = $eventManager;
		$this->_session = $customerSession;
		$this->_customerFactory = $customerFactory;
		$this->_customerRepository = $customerRepository;
		$this->_authentication = $authentication;
		$this->_cookieManager = $cookieManager;
		$this->_cookieMetadataFactory = $cookieMetadataFactory;	
		$this->_dataObjectHelper = $dataObjectHelper;
		$this->_helper = $helper;
		$this->_mathRandom = $mathRandom;
    }
    
    /**
     * Retrieve new account status
     *
     * @return bool
     */
    public function isNewAccount()
    {
        return $this->_isNewAccount;
    }

    /**
     * Set new account status flag
     *
     * @param bool $flag
     * @return $this
     */
    protected function _setIsNewAccount($flag = true)
    {
        $this->_isNewAccount = $flag;
        return $this;
    }
    
    /**
     * Retrieve Popup Close URL
     * 	 
     * @return string
     */
    public function getPopupCloseUrl()
    {
        return $this->_store->getUrl($this->_popupCloseUrl);
    }
    
    /**
     * Retrieve Account URL
     * 	 
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_store->getUrl($this->_accountUrl);
    }
    
    /**
     * Retrieve Store
     * 	 
     * @return Store
     */
    public function getStore()
    {
        return $this->_store;
    }
    
    /**
     * Set Store
     * 
     * @param Store $store     
     * @return \Faonni\SocialLogin\Model\AccountManagement
     */
    public function setStore(Store $store)
    {
        $this->_store = $store;
        return $this;
    }
    
    /**
     * initiate Customer By Profile
     * 
     * @param Profile $profile
     * @param ProfileData $data	 
     * @return \Faonni\SocialLogin\Model\AccountManagement
     */
    public function initiateByProfile(Profile $profile, ProfileData $data)
    {
        if ($this->_session->isLoggedIn()) {
            $this->_accountUrl = 'customer/account/socialProfile';
            if (!$profile->getId()) {
				/* save profile */
				$profile->setData($data->getData())
					->setCustomerId($this->_session->getCustomerId())
					->save();
				// add check email	
            }
        } else {
            if (!$profile->getId()) {
				/* create account */
				$customer = $this->_accountManagement->createAccount(
					$this->extractProfileData($data),
					$this->generatePassword()
				);
				/* save profile */
				$profile->setData($data->getData())
					->setCustomerId($customer->getId())
					->save();
				/* set customer as LoggedIn */	
				$this->setCustomerDataAsLoggedIn($customer);
				 $this->_setIsNewAccount();
            } else {
				$customer = $this->authenticate($profile);
				$this->setCustomerDataAsLoggedIn($customer);			
            }
        }      
    }
	
    /**
     * Retrieve random password from required character sets
     *
     * @param int $length
     * @return string
     */
    public function generatePassword($length = 12)
    {
        $string = '';
		foreach ([
			Random::CHARS_UPPERS, 
			Random::CHARS_LOWERS, 
			Random::CHARS_DIGITS] as $i => $chars
		) {
			$partLength = $this->_mathRandom->getRandomNumber(1, $length - (2 - (int)$i));
			$length = $length - $partLength; 
			$string.= $this->_mathRandom->getRandomString($partLength, $chars);
		}
		$chars = str_split($string);
		/* randomizes the order of the elements */
		shuffle($chars);
		return implode('', $chars);
    }
	
    /**
     * Extract customer account by Social Profile
     *
     * @param ProfileData $profileData
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function extractProfileData(ProfileData $profileData) 
	{
		$customer = $this->_customerFactory->create();
        $this->_dataObjectHelper->populateWithArray(
            $customer,
            $profileData->getData(),
            '\Magento\Customer\Api\Data\CustomerInterface'
        );		
		$customer->setWebsiteId($this->_store->getWebsiteId());
		$customer->setStoreId($this->_store->getId());
		$customer->setGroupId(
			$this->_helper->getCustomerDefaultGroupId($this->_store->getId())
		);		
		return $customer;
    }
	
    /**
     * Authenticate a Customer by Social Profile
     *
     * @param Profile $profile
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\State\UserLockedException
     */
    public function authenticate(Profile $profile)
	{
		$customerId = $profile->getCustomerId();
        if ($this->_authentication->isLocked($customerId)) {
            throw new UserLockedException(__('The account is locked.'));
        }
		// add event
		return $this->_customerRepository->getById($customerId);		
	}
	
    /**
     * Set Customer as LoggedIn
     *
     * @param CustomerData $customer
     * @return \Faonni\SocialLogin\Model\AccountManagement
     */
    public function setCustomerDataAsLoggedIn($customer)
    {
		$this->_session->setCustomerDataAsLoggedIn($customer);
		$this->_session->regenerateId();
		if ($this->_cookieManager->getCookie('mage-cache-sessid')) {
			$metadata = $this->_cookieMetadataFactory->createCookieMetadata();
			$metadata->setPath('/');
			$this->_cookieManager->deleteCookie('mage-cache-sessid', $metadata);
		}
        return $this;
    }
}
