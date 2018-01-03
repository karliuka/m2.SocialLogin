<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\ResourceModel\Provider;

use Faonni\SocialLogin\Model\ResourceModel\ProviderAbstract;
use Faonni\SocialLogin\Model\Provider;

/**
 * Linkedin oauth2 provider resource model
 */
class Linkedin extends ProviderAbstract
{
    /**
     * The token URL
	 *
     * @var string
     */
	protected $_tokenUrl = 'https://www.linkedin.com/oauth/v2/accessToken';
	
    /**
     * The URL used when authenticating a user
	 *	 
     * @var string
     */
	protected $_oauthUrl = 'https://www.linkedin.com/oauth/v2/authorization';
	
    /**
     * The api URL
	 *
     * @var string
     */
	protected $_apiUrl = 'https://api.linkedin.com/v1/people/~:(id,first-name,last-name,picture-url,email-address)?format=json';	
	
    /**
     * The URL used when authenticating a user after the question mark ?
	 *	 
     * @var array
     */	
	protected $_oauthQuery = [
	
    /**
     * Identifies the Provider API access that your application is requesting
     */	
	'scope' => 'r_basicprofile r_emailaddress',	
	
    /**
     * Provides any state that might be useful to your application upon receipt of the response. The 
     * Provider Authorization Server roundtrips this parameter,so your application receives the same 
     * value it sent. Possible uses include redirecting the user to the correct resource in your 
     * site, nonces, and cross-site-request-forgery mitigations.
     */		
	'state' => '',
	
    /**
     * Determines whether the Provider OAuth 2.0 endpoint returns an authorization code. Web server 
     * applications should use code.
     */		
	'response_type' => 'code',
	
    /**
     * Identifies the client that is making the request. The value passed in this parameter must 
     * exactly match the value shown in the Provider Developers Console.
     */		
	'client_id' => '',	
	
    /**
     * Determines where the response is sent. The value of this parameter must exactly match one of 
     * the values listed for this project in the Provider Developers Console (including the http or 
     * https scheme, case, and trailing '/').
     */		
	'redirect_uri' => '',
	];
	
    /**
     * Determines how the Dialog is Provider Rendered
	 *	 
     * @var string
     */
	protected $_displayMode = '';
	
    /**
     * Set Scope
	 *	 
     * @param string $scope 
     * @return Faonni_SocialLogin_Model_Resource_Abstract
     */	
	public function setScope($scope)
	{
		$this->_oauthQuery['scope'] = $scope;
		return $this;
	}
	
    /**
     * Retrieve Scope
	 *	 
     * @return string
     */		
	public function getScope()
	{
		return $this->_oauthQuery['scope'];
	}
	
    /**
     * Retrieve Provider URL
	 *
     * @param Provider $provider
     * @param string $target
     * @param string $additional 	 
     * @return string
     */	
	public function getProviderUrl(Provider $provider, $target, $additional = '')
	{
		$this->_oauthQuery = array_merge(
			$this->_oauthQuery, [
				'client_id'    => $provider->getApiKey(),
				'redirect_uri' => $provider->getRedirectUrl(),
				'state'        => $this->getState($provider, $target, $provider->getStore(true)->getId(), $additional)
		]);
		return $this->_oauthUrl . '?' . http_build_query($this->_oauthQuery);		
	}
	
    /**
     * Obtain user information from the ID token
	 *
     * @param Provider $provider
     * @return \Magento\Framework\DataObject
     */
	public function getProfileData(Provider $provider) 
	{
		return $this->fetchProfile(
            $this->getClient($this->_apiUrl)
                ->setHeaders('Authorization', 'Bearer ' . $provider->getToken())
                ->request(\Zend_Http_Client::GET)
		);		
	}
	
    /**
     * Validate Profile
	 *
     * @param \stdClass $data	 
     * @return bool
     */
    public function validateProfile($data)
    {
        /* check required values */
        $varNames = ['id', 'firstName', 'lastName', 'emailAddress'];
        foreach ($varNames as $var) {
            if (empty($data->{$var})) return false;
        }
        /* check email address */
        return \Zend_Validate::is($data->emailAddress, 'EmailAddress');
    }
    
    /**
     * Retrieve Convert Profile
	 *
     * @param \stdClass $data	 
     * @return 
     */
    public function convertProfile($data) 
	{
		$profileData = $this->_profileDataFactory->create();
		/* convert data to profileData */
		$profileData
			->setProviderId('linkedin')
			->setProviderUid($data->id)
            ->setFirstname($data->firstName)
            ->setLastname($data->lastName)
            ->setEmail($data->emailAddress);
		
		return $profileData;
    }
	
    /**
     * Retrieve Raw Post Data string
	 *	 
     * @param Provider $provider	 
     * @param string $code	
     * @return string
     */	
	public function getRawPostData(Provider $provider, $code)
	{
		return http_build_query([
			'code'          => $code,
			'grant_type'    => 'authorization_code',
			'redirect_uri'  => $provider->getRedirectUrl(),
			'client_id'     => $provider->getApiKey(),
			'client_secret' => $provider->getSecret()
		]);		
	}	
}
