<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\ResourceModel;

use Faonni\SocialLogin\Model\Provider;

/**
 * Oauth2 Provider Resource interface
 */
interface ProviderInterface
{
    /**
     * Set Scope
	 *
     * @param string $scope 
     * @return \Faonni\SocialLogin\Model\Provider\ProviderInterface
     */	
	public function setScope($scope);
	
    /**
     * Retrieve Scope
	 *
     * @return string
     */		
	public function getScope();
	
    /**
     * Retrieve State code
	 *
     * @param Provider $provider
     * @param string $target
     * @param integer $storeId     
     * @param string $additional	 
     * @return string
     */	
	public function getState(Provider $provider, $target, $storeId, $additional = '');
	
    /**
     * Retrieve Provider URL
	 *
     * @param Provider $provider
     * @param string $target
     * @param string $additional 	 
     * @return string
     */	
	public function getProviderUrl(Provider $provider, $target, $additional = '');
	
    /**
     * Obtain user information from the ID token
	 *
     * @param Provider $provider
     * @return \Magento\Framework\DataObject
     */
	public function getProfileData(Provider $provider);
	
    /**
     * Retrieve Profile data
	 *
     * @param \Zend_Http_Response $response	 
     * @return bool|\Magento\Framework\DataObject 
     */
    public function fetchProfile(\Zend_Http_Response $response);
	
    /**
     * Validate Profile
	 *
     * @param \stdClass $data	 
     * @return bool
     */
    public function validateProfile($data);	

    /**
     * Retrieve Convert Profile
	 *
     * @param \stdClass $data	 
     * @return \Magento\Framework\DataObject
     */
    public function convertProfile($data);	
	
	/**
     * Obtain an Access Token that Grants Access to Provider API
	 *
     * @param Provider $provider
     * @param string $code	 
     * @return string
     */
    public function obtainToken(Provider $provider, $code);
	
    /**
     * Retrieve Access Token
	 *
     * @param \Zend_Http_Response $response	 
     * @return string|bool
     */
    public function fetchToken(\Zend_Http_Response $response);
	
    /**
     * Retrieve Raw Post Data string
	 *	 
     * @param Provider $provider	 
     * @param string $code	
     * @return string
     */	
	public function getRawPostData(Provider $provider, $code);	
}