<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreRepository;
use Psr\Log\LoggerInterface;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\AccountManagementFactory;
use Faonni\SocialLogin\Model\ProfileFactory;
use Faonni\SocialLogin\Model\Provider;

/**
 * Obtain Token Observer
 */
class ObtainTokenObserver implements ObserverInterface
{
    /**
     * Target Store Id
     *
     * @var string
     */
    protected $_storeId;

    /**
     * Store Repository
     *
     * @var \Magento\Store\Model\StoreRepository
     */
    protected $_storeRepository;
    
    /**
     * SocialLogin helper
     *
     * @var \Faonni\SocialLogin\Helper\Data
     */
    protected $_helper; 
    
    /**
     * SocialLogin profile
     *
     * @var \Faonni\SocialLogin\Model\Profile
     */
    protected $_profile;     
    
    /**    
     * Result Redirect Factory
     *   
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $_resultRedirectFactory;  
    
    /**
     * Account Management
     *
     * @var \Faonni\SocialLogin\Model\AccountManagement
     */
    protected $_accountManagement;
	
    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    
    /**
     * Message Manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager; 
    
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */    
    protected $_escaper;    
	
    /**
     * Initialize observer
     *
     * @param StoreRepository $storeRepository
     * @param RedirectFactory $resultRedirectFactory
     * @param SocialLoginHelper $helper
     * @param ProfileFactory $profile    
     * @param AccountManagementFactory $accountManagementFactory 
     * @param LoggerInterface $logger 
     * @param ManagerInterface $messageManager
     * @param Escaper $escaper     
     */
    public function __construct(
        StoreRepository $storeRepository,
        RedirectFactory $resultRedirectFactory,
        SocialLoginHelper $helper,
        ProfileFactory $profileFactory,
        AccountManagementFactory $accountManagementFactory,
		LoggerInterface $logger,
		ManagerInterface $messageManager,
		Escaper $escaper
    ) {
        $this->_storeRepository = $storeRepository;
        $this->_resultRedirectFactory = $resultRedirectFactory;
        $this->_helper = $helper;
        $this->_profile = $profileFactory->create();
        $this->_accountManagement = $accountManagementFactory->create();
		$this->_logger = $logger;
		$this->_messageManager = $messageManager;
		$this->_escaper = $escaper;
    }

    /**
     * Handler for obtain token event
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {		
		if (!$this->_helper->isEnabled()) {
            return;
		}
		/** @var \Faonni\SocialLogin\Model\Provider $provider */
		$provider = $observer->getEvent()->getProvider();
		try {
            foreach ($this->_storeRepository->getList() as $store) {
                /* math state code */
                if ($provider->isValidState(Provider::SCOPE_PREFIX, $store->getId())) {
                    $this->_storeId = $store->getId();
                    $profileData = $provider->getProfileData();
                    /* load profile */
                    $fields = array(
                        'provider_id'  => $provider->getId(), 
                        'provider_uid' => $profileData->getProviderUid()
                    );
                    $profile = $this->_profile->loadByFields($fields);
                    /* initiate account */
                    $this->_accountManagement->initiateByProfile($profile, $store, $profileData);
                    /* prepare result */
                    $resultRedirect = $this->_resultRedirectFactory->create();
                    $resultRedirect->setUrl(
                        $this->_accountManagement->getRedirectUrl()
                    ); 
                    /* prepare success message */
                    if ($this->_accountManagement->isNewAccount()) {
                        $this->_messageManager->addSuccess(
                            __('Thank you for registering with %1.', $store->getFrontendName())
                        );
                    }
                    /** @var \Magento\Framework\DataObject $response */
                    $response = $observer->getEvent()->getResponse();                
                    $response->setResult($resultRedirect); 
                    break;
                }
            }
        }
        /* state exception */
        catch (StateException $e) {
            $url = $this->_helper->getStore($this->_storeId)->getUrl('customer/account/forgotpassword');
            $this->_messageManager->addError(
                __('There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.', $url)
            );
        } 
        /* input exception */
        catch (InputException $e) {
            $this->_messageManager->addError(
                $this->_escaper->escapeHtml($e->getMessage())
            );
            foreach ($e->getErrors() as $error) {
                $this->_messageManager->addError(
                    $this->_escaper->escapeHtml($error->getMessage())
                );
            }
        } 
        /* localized exception */
        catch (LocalizedException $e) {
            $this->_messageManager->addError(
                $this->_escaper->escapeHtml($e->getMessage())
            );
        } 
        /* other exception */
        catch (\Exception $e) {
            $this->_messageManager->addException($e, __('We can\'t save the customer.'));
        }
    }
}  
