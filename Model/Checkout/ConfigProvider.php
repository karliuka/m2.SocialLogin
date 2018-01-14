<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\ProviderFactory;

/**
 * SocialLogin Config Provider
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Helper
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
	 * Initialize Config
	 *	
     * @param SocialLoginHelper $helper
     * @param ProviderFactory $providerFactory   
     */
    public function __construct(
        SocialLoginHelper $helper,
        ProviderFactory $providerFactory
    ) {
        $this->_helper = $helper;
        $this->_provider = $providerFactory->create();
    } 

    /**
     * Retrieve Assoc Array Of Checkout Configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return ['sociallogin' => [
			'popup' => $this->_helper->isPopupMode(),
			'providers' => $this->getCollection()
		]];
    }
    
    /**
     * Retrieve Provider Collection
     * 	    
     * @return \Faonni\SocialLogin\Model\ResourceModel\Provider\Collection
     */
    public function getCollection()
    {   
		$providers = [];
		$collection = $this->_provider->getCollection();				
		foreach ($collection as $key => $provider) {
			if (!$provider->isAvailable()) {
				continue;
			}
            $providers[] = [
                'id' => $provider->getId(),
                'width'  => $provider->getWidth(),
                'height' => $provider->getHeight(),
                'url'    => $provider->getUrl(),
                'title'  => $provider->getTitle()
            ];			
		}
		return $providers;
    }     
}
