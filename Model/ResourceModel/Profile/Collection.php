<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\ResourceModel\Profile;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Profile ResourceModel Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
			'Faonni\SocialLogin\Model\Profile', 
			'Faonni\SocialLogin\Model\ResourceModel\Profile'
		);
    }
}
