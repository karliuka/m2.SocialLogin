<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Faonni_SocialLogin InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module Faonni_SocialLogin
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
		
        /**
         * Create table 'faonni_sociallogin_profile'
         */		
        $tableName = 'faonni_sociallogin_profile';
        if (!$installer->tableExists($tableName)) {
            $table = $connection->newTable(
					$installer->getTable($tableName)
				)
				->addColumn(
                    'profile_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'identity' => true, 'nullable' => false, 'primary' => true],
                    'Profile Id'
                )
				->addColumn(
                    'provider_id',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false],
                    'Provider'
                )
				->addColumn(
                    'provider_uid',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Provider UID'
                )
				->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer Id'
                )
				->addColumn(
                    'firstname',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer Firstname'
                )
				->addColumn(
                    'lastname',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer Lastname'
                ) 
				->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer Email'
                )                 
				->addColumn(
					'created_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Creation Time'
				)
				->addColumn(
					'updated_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Update Time'
				)
				->addIndex(
					$installer->getIdxName($tableName, ['provider_id']),
					['provider_id']
				)	
				->addIndex(
					$installer->getIdxName($tableName, ['provider_uid']),
					['provider_uid']
				)									
				->addIndex(
					$installer->getIdxName($tableName, ['customer_id']),
					['customer_id']
				)					
				->addIndex(
					$installer->getIdxName(
						$tableName, ['provider_id', 'provider_uid'], AdapterInterface::INDEX_TYPE_UNIQUE),
						['provider_id', 'provider_uid'], ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
				)				
				->addForeignKey(
					$installer->getFkName($tableName, 'customer_id', 'customer_entity', 'entity_id'),
					'customer_id', $installer->getTable('customer_entity'), 'entity_id', Table::ACTION_CASCADE
				)									
				->setComment(
                    'Faonni SocialLogin Profile Table'
                );				
            $connection->createTable($table);
		}	                                           
        $installer->endSetup();
    }
}
