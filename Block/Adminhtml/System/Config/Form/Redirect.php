<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;

/**
 * Render Redirect field html element in Stores Configuration
 */
class Redirect extends Field
{
    /**
     * SocialLogin helper
     *
     * @var \Faonni\SocialLogin\Helper\Data
     */
    protected $_helper; 
    
    /**
	 * Initialize field
	 *	
     * @param Context $context
     * @param SocialLoginHelper $helper     
     * @param array $data
     */
    public function __construct(
        Context $context,
        SocialLoginHelper $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        
        parent::__construct(
            $context, 
            $data
        );
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element
            ->setReadonly(true)
            ->setValue(
                $this->_helper->getRedirectUrl(
                    $element->getFieldConfig('provider_id')
                )
            );        
        return parent::render($element);
    }
}
 
