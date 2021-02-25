<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 *
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Faonni_SocialLogin Uninstall
 */
class Uninstall implements UninstallInterface
{
    /**
     * Uninstall DB schema for a module Faonni_SocialLogin
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        /**
         * Remove table 'faonni_sociallogin_profile'
         */
        $tableName = 'faonni_sociallogin_profile';
        if ($installer->tableExists($tableName)) {
            $connection->dropTable($installer->getTable($tableName));
        }
        $installer->endSetup();
    }
}
