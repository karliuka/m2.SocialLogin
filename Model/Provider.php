<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\ResourceModel\ProviderFactory as ResourceProviderFactory;
use Faonni\SocialLogin\Model\ResourceModel\Provider\CollectionFactory;

/**
 * Provider Model
 */
class Provider extends DataObject
{
    /**
     * Scope prefix
	 *
     * @var string
     */
	const SCOPE_PREFIX = 'profile';
	
    /**
     * SocialLogin helper
     *
     * @var \Faonni\SocialLogin\Helper\Data
     */
    protected $_helper; 
    
    /**
     * Oauth2 Provider Resource Factory
     *
     * @var \Faonni\SocialLogin\Model\ProviderFactory
     */
    protected $_resourceFactory;
    
    /**
     * Oauth2 Provider Resource model
     * 	 
     * @var \Faonni\SocialLogin\Model\ResourceModel\ProviderInterface
     */
    protected $_resource;
    
    /**
     * Providers Collection
     *
     * @var \Faonni\SocialLogin\Model\ResourceModel\Provider\Collection
     */
    protected $_collection;

    /**
     * Providers Collection Factory
     *
     * @var \Faonni\SocialLogin\Model\ResourceModel\Provider\CollectionFactory
     */
    protected $_collectionFactory;    
    
    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;    
	
    /**
     * Access token from the Provider Authorization Server
     * 	 
     * @var string
     */
    protected $_token;
	
    /**
     * The State string
     * 	 
     * @var string
     */
    protected $_state;    
    
    /**
	 * Initialize Model
	 *	
     * @param ResourceProviderFactory $resourceFactory
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory 
     * @param SocialLoginHelper $helper     
     */
    public function __construct(
        ResourceProviderFactory $resourceFactory,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        SocialLoginHelper $helper
    ) {
        $this->_resourceFactory = $resourceFactory;
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_helper = $helper;
    } 
    
    /**
     * Retrieve Provider Resource
     * 	 
     * @return \Faonni\SocialLogin\Model\ResourceModel\ProviderInterface
     */
    public function getResource()
    {
        if (null === $this->_resource) {			
			$this->_resource = $this->_resourceFactory->create($this->getId());
        }
        return $this->_resource;
    }
    
    /**
     * Retrieve Provider Collection
     * 	 
     * @throws \Magento\Framework\Exception\LocalizedException     
     * @return \Faonni\SocialLogin\Model\ResourceModel\Provider\Collection
     */
    public function getCollection()
    {
        if (null === $this->_collection){
            $this->_collection = $this->_collectionFactory->create();
		}
		return $this->_collection;
    }
    
    /**
     * Retrieve Application Store object
     *
     * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($storeId=null)
    {
        return $this->_storeManager->getStore($storeId);
    }
    
    /**
     * Retrieve init Provider URL
     * 
     * @param array $params   
     * @return string
     */
    public function getUrl($params=[])
    {
		return $this->getStore()->getUrl(
            'customer/account/initProvider', array_merge($params, ['id' => $this->getId()])
        );
    }
	
    /**
     * Retrieve redirect Provider URL
     * 	 
     * @return string
     */
    public function getRedirectUrl()
    {
		return $this->_helper->getRedirectUrl($this->getId());
    }
    
    /**
     * Check whether Provider can be used
     * 	 
     * @return bool
     */
    public function isAvailable()
    {
		return (bool) $this->getActive() && $this->getApiKey() && $this->getSecret();
    }  
    
    /**
     * Retrieve Provider URL
     * 	 
     * @param string $target
	 * @param string $additional
     * @return string
     */
	public function getProviderUrl($target, $additional='')
	{
		return $this->getResource()->getProviderUrl($this, $target, $additional);
	}
	
    /**
     * Retrieve State code
     * 	 
     * @param string $target
     * @param integer $storeId      
	 * @param string $additional
     * @return string
     */	
	public function getState($target, $storeId, $additional='')
	{
		return $this->getResource()->getState($this, $target, $storeId, $additional);
	}
	
    /**
     * Set State code
     * 	 
     * @param string $state	 
     * @return \Faonni\SocialLogin\Model\Provider
     */	
	public function setState($state)
	{
		$this->_state = $state;
		return $this;
	}
	
    /**
     * Retrieve Request State code
     * 	 
     * @return string
     */		
	public function getRequestState()
	{
		return $this->_state;
	}
	
    /**
     * Set Request State code
     * 	 
     * @param string $state	 
     * @return \Faonni\SocialLogin\Model\Provider
     */	
	public function setRequestState($state)
	{
		return $this->setState($state);
	}
	
    /**
     * Retrieve Scope
     * 	 
     * @return string
     */		
	public function getScope()
	{
		return $this->getResource()->getScope();
	}
	
    /**
     * Set Scope
     * 	 
     * @param string $scope 
     * @return \Faonni\SocialLogin\Model\Provider
     */	
	public function setScope($scope)
	{
		$this->getResource()->setScope($scope);
		return $this;
	}
	
    /**
     * Validate State
     * 	 
     * @param string $target
     * @param integer $storeId     
	 * @param string $additional	 
     * @return bool
     */	
	public function isValidState($target, $storeId, $additional='')
	{
		return $this->_state && $this->_state == $this->getState($target, $storeId, $additional);
	}
	
    /**
     * Retrieve Oauth2 Request Tokens as string
     * 	 
     * @return string
     */
    public function getToken() 
	{
		return $this->_token;
    }
	
    /**
     * Obtain an Access Token that grants access to Provider API
     * 	 
     * @param string $code	 
     * @return string
     */
    public function obtainToken($code) 
	{
		$this->_token = $this->getResource()->obtainToken($this, $code);
		return $this->_token 
            ? true 
            : false;
    }
	
    /**
     * Retrieve Profile Data	
	 *
     * @return Varien_Object
     */		
    public function getProfileData()
    {
        return $this->getResource()->getProfileData($this);
    }
	
    /**
     * Load Provider data
     * 	 
     * @param string $id
     * @return \Faonni\SocialLogin\Model\Provider
     */
    public function load($id)
    {
        return $this->getCollection()->getItemByColumnValue('id', $id);
    }    
}
