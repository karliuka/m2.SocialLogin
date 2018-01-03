<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\ResourceModel;

use Magento\Framework\DataObjectFactory;
use Psr\Log\LoggerInterface;
use Faonni\SocialLogin\Model\Profile\DataFactory;
use Faonni\SocialLogin\Model\Provider;

/**
 * Oauth2 Provider Abstract model
 */
abstract class ProviderAbstract implements ProviderInterface
{
    /**
     * Profile Data Factory
     *
     * @var \Faonni\SocialLogin\Model\Profile\DataFactory
     */
    protected $_profileDataFactory;
    
    /**
     * The token URL
	 *
     * @var string
     */
	protected $_tokenUrl;
	
    /**
     * The URL used when authenticating a user
	 *
     * @var string
     */
	protected $_oauthUrl;
	
    /**
     * The api URL
	 *
     * @var string
     */
	protected $_apiUrl;	
	
    /**
     * The URL used when authenticating a user after the question mark ?
	 *
     * @var array
     */	
	protected $_oauthQuery;
	
    /**
     * Determines how the Dialog is Provider Rendered
	 *
     * @var string
     */
	protected $_displayMode = '&display=popup';
	
    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger; 	
	
    /**
	 * Initialize model
	 *	
     * @param DataFactory $profileDataFactory
     * @param LoggerInterface $logger     
     */
    public function __construct(
        DataFactory $profileDataFactory,
		LoggerInterface $logger
    ) {
        $this->_profileDataFactory = $profileDataFactory;
		$this->_logger = $logger;
    } 

    /**
     * Set Scope
	 *
     * @param string $scope 
     * @return \Faonni\SocialLogin\Model\ResourceModel\ProviderInterface
     */	
	abstract public function setScope($scope);
	
    /**
     * Retrieve Scope
	 *
     * @return string
     */		
	abstract public function getScope();
	
    /**
     * Retrieve State code
	 *
     * @param Provider $provider
     * @param string $target
     * @param integer $storeId     
     * @param string $additional	 
     * @return string
     */	
	public function getState(Provider $provider, $target, $storeId, $additional = '')
	{
		return md5(
			$provider->getId() . '.' . $target . '.' . $storeId . '.' . $additional
		);
	}
	
    /**
     * Retrieve Provider URL
	 *
     * @param Provider $provider
     * @param string $target
     * @param string $additional 	 
     * @return string
     */	
	abstract public function getProviderUrl(Provider $provider, $target, $additional = '');
	
    /**
     * Obtain user information from the ID token
	 *
     * @param Provider $provider
     * @return \Magento\Framework\DataObject
     */
	abstract public function getProfileData(Provider $provider);
	
    /**
     * Retrieve profile data
	 *
     * @param \Zend_Http_Response $response	 
     * @return bool|\Magento\Framework\DataObject 
     */
    public function fetchProfile(\Zend_Http_Response $response) 
	{
		if ($response->isSuccessful()) {
			$data = json_decode($response->getBody());
			if ($this->validateProfile($data)) {
				return $this->convertProfile($data);
			}
		}
		return false; 
    }
	
    /**
     * Validate Profile
	 *
     * @param \stdClass $data	 
     * @return bool
     */
    abstract public function validateProfile($data);	

    /**
     * Retrieve Convert Profile
	 *
     * @param \stdClass $data	 
     * @return \Magento\Framework\DataObject
     */
    abstract public function convertProfile($data);	
	
	/**
     * Obtain an Access Token that Grants Access to Provider API
	 *
     * @param Provider $provider
     * @param string $code	 
     * @return string
     */
    public function obtainToken(Provider $provider, $code)
    {
		return $this->fetchToken(
            $this->getClient($this->_tokenUrl)
                ->setRawData($this->getRawPostData($provider, $code))
                ->setHeaders('Content-Type', 'application/x-www-form-urlencoded')
                ->request(\Zend_Http_Client::POST)
		);
    }
    
    /**
     * Retrieve Access Token
	 *
     * @param \Zend_Http_Response $response	 
     * @return string|bool
     */
    public function fetchToken(\Zend_Http_Response $response) 
	{		
		if ($response->isSuccessful()) {
			$data = json_decode($response->getBody());
			if(!empty($data->access_token)) {
                return (string)$data->access_token;
            }
		}
		return false; 
    }
    
    /**
     * Retrieve Raw Post Data string
	 *	 
     * @param Provider $provider	 
     * @param string $code	
     * @return string
     */	
	abstract public function getRawPostData(Provider $provider, $code);    

    /**
     * Retrieve the Zend Http Client
	 *
     * @param string $url	 
     * @return \Zend_Http_Client
     */
    public function getClient($url) 
	{
		return new \Zend_Http_Client($url, [
			'adapter'     => 'Zend_Http_Client_Adapter_Curl',
			'curloptions' => [CURLOPT_SSL_VERIFYPEER => false],
		]);
    }	
}
