<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var Mage_Core_Model_Resource_Setup $this */
$this->startSetup();

/* @var $connection Varien_Db_Adapter_Interface */
$connection = $this->getConnection();

/**
 * Create new table 'webhook_subscription'
 */
$subscriptionTable = $connection->newTable($this->getTable('webhook_subscription'))
    ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
        array('identity'  => true, 'unsigned'  => true, 'nullable'  => false, 'primary'   => true),
        'Subscription Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array('nullable' =>false),
        'Subscription Name')
    ->addColumn('registration_mechanism', Varien_Db_Ddl_Table::TYPE_TEXT, 40,
        array('nullable' =>false),
        'Registration Mechanism')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array('unsigned' => true, 'nullable' => false, 'default'  => 0),
        'Status')
    ->addColumn('version', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(),
        'Extension Version')
    ->addColumn('alias', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(),
        'Alias')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(),
        'Updated At')
    ->addColumn('endpoint_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
        array('nullable' => true,'default'  => NULL,'unsigned' => true),
        'Subscription Endpoint')
    ->addForeignKey(
        'FK_WEBHOOK_SUBSCRIPTION_ENDPOINT_ID',
        'endpoint_id',
        $this->getTable('outbound_endpoint'),
        'endpoint_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setOption('collate', null)
    ->setOption('comment', 'Subscription');
$connection->createTable($subscriptionTable);

/**
 * Create table 'webhook_subscription_hook'
 */
$hookTable = $connection->newTable($this->getTable('webhook_subscription_hook'))
    ->addColumn('hook_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Hook Id')
    ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Subscription Id')
    ->addColumn('topic', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Hook Topic')
    ->addIndex(
        $this->getIdxName('webhook_subscription_hook', array('topic')),
        array('topic'))
    ->addForeignKey(
        'FK_WEBHOOK_SUBSCRIPTION_SUBSCRIPTION_ID',
        'subscription_id',
        $this->getTable('webhook_subscription'),
        'subscription_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setOption('collate', null)
    ->setOption('comment', 'Webhook');
$connection->createTable($hookTable);

/**
 * Create table 'webhook_event'
 */
$eventTable = $connection->newTable($this->getTable('webhook_event'))
    ->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
        array('identity'  => true, 'unsigned'  => true, 'nullable'  => false, 'primary'   => true),
        'Event Id')
    ->addColumn('topic', Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array('nullable' => false), 'Hook Topic')
    ->addColumn('body_data', Varien_Db_Ddl_Table::TYPE_VARBINARY, '4M',
        array('nullable' => false),
        'Serialized Data Array')
    ->addColumn('headers', Varien_Db_Ddl_Table::TYPE_TEXT, '16k', array(),
        'Headers')
    ->addColumn('options', Varien_Db_Ddl_Table::TYPE_TEXT, '16k', array(),
        'Options')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('nullable' => false),
        'Status')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array('nullable' => false),
        'Updated At')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array('nullable' => false),
        'Created At')
    ->addIndex($this->getIdxName('webhook_event', array('status')), array('status'))
    ->setOption('collate', null)
    ->setOption('comment', 'Queued Event Data');
$connection->createTable($eventTable);

/**
 * Create table 'webhook_dispatch_job'
 */
$dispatchJobTable = $connection->newTable($this->getTable('webhook_dispatch_job'))
    ->addColumn('dispatch_job_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity'  => true, 'unsigned'  => true, 'nullable'  => false, 'primary'   => true),
        'Dispatch Job Id')
    ->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array('unsigned'  => true, 'nullable'  => false),
        'Event Id')
    ->addColumn('subscription_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array('unsigned'  => true, 'nullable'  => false),
        'Subscription Id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array('nullable'  => false, 'default'  => '0'),
        'Status')
    ->addColumn('retry_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array('unsigned'  => true, 'nullable'  => false),
        'Retry Count')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
        array('default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE, 'nullable' => false),
        'Updated At')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
        array('default' => '0000-00-00 00:00:00', 'nullable' => false),
        'Created At')
    ->addColumn('retry_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
        array('default' => '0000-00-00 00:00:00', 'nullable' => false),
        'Retry At')
    ->addForeignKey(
        'FK_WEBHOOK_SERVICE_DISPATCHER_ID',
        'subscription_id',
        $this->getTable('webhook_subscription'),
        'subscription_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        'FK_WEBHOOK_MESSAGE_DISPATCHER_ID',
        'event_id',
        $this->getTable('webhook_event'),
        'event_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setOption('collate', null)
    ->setOption('comment', 'Dispatch Jobs');
$connection->createTable($dispatchJobTable);

/**
 * Create table 'outbound_endpoint' *
 */
$outboundEndpointTbl = $connection->newTable($this->getTable('outbound_endpoint'))
    ->addColumn('endpoint_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
        array('identity'  => true, 'unsigned'  => true, 'nullable'  => false, 'primary'   => true),
        'Endpoint Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array('nullable' =>false),
        'Endpoint Name')
    ->addColumn('endpoint_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array('nullable' =>false),
        'Endpoint URL')
    ->addColumn('authentication_type', Varien_Db_Ddl_Table::TYPE_TEXT, 40,
        array('nullable' =>false),
        'Authentication Type')
    ->addColumn('format', Varien_Db_Ddl_Table::TYPE_TEXT, 40,
        array('nullable' =>false),
        'Data Format')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array('unsigned' => true, 'nullable' => false, 'default'  => 0),
        'Status')
    ->addColumn('alias', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(),
        'Alias')
    ->addColumn('api_user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                                                                             'unsigned' => true, 'nullable' => true),
        'Webapi User Id')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(),
        'Updated At')
    ->addColumn('timeout_in_secs', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array( 'nullable' => false, 'default' => 5),
        'Timeout in seconds')
    ->addIndex(
        $this->getIdxName('outbound_endpoint', array('endpoint_id', 'alias'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('endpoint_id', 'alias'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $this->getFkName('outbound_endpoint', 'api_user_id', 'webapi_user', 'user_id'),
        'api_user_id',
        $this->getTable('webapi_user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setOption('collate', null)
    ->setOption('comment', 'Endpoint for outbound messages');
$connection->createTable($outboundEndpointTbl);

$this->endSetup();