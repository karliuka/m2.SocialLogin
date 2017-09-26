<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Faonni SocialLogin Data helper
 */
class Data extends AbstractHelper
{
    /**
     * Enabled config path
     */
    const XML_CONFIG_ENABLED = 'faonni_sociallogin/storefront/active';
    	
    /**
     * Customer default group config path
     */
    const XML_CONFIG_DEFAULT_GROUP = 'faonni_sociallogin/storefront/default_group';

    /**
     * Popup config path
     */
    const XML_CONFIG_POPUP = 'faonni_sociallogin/storefront/popup';
    
    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager; 
    
    /**
	 * Initialize helper
	 *	
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        
        parent::__construct(
            $context
        );
    } 
    
    /**
     * Check Social Login functionality should be enabled
     *
     * @param string $store	 
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->_getConfig(self::XML_CONFIG_ENABLED, $store);
    } 
    
    /**
     * Check Popup mode
     *
     * @param string $store	 
     * @return bool
     */
    public function isPopupMode($store = null)
    {
        return $this->_getConfig(self::XML_CONFIG_POPUP, $store);
    } 
    
    /**
     * Retrieve Customer Default GroupId
     *
     * @param string $store 
     * @return string
     */
    public function getCustomerDefaultGroupId($store = null)
    {
        return $this->_getConfig(self::XML_CONFIG_DEFAULT_GROUP, $store);
    } 
    
    /**
     * Retrieve redirect Provider URL
     * 
     * @param string $probiderId
     * @return string
     */
    public function getRedirectUrl($probiderId)
    {
		return $this->getStore(Store::DEFAULT_STORE_ID)
            ->getBaseUrl() . 'customer/account/oauth/id/' . $probiderId . '/';
    }
    
    /**
     * Retrieve Application Store object
     *
     * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($storeId = null)
    {
        return $this->_storeManager->getStore($storeId);
    }
    
    /**
     * Retrieve store configuration data
     *
     * @param string $path
     * @param int|Store $store	 
     * @return string|null
     */
    protected function _getConfig($path, $store = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $store);
    }   
}
