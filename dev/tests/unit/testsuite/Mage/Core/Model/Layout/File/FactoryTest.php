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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Core_Model_Layout_File_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_File_Factory
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('Magento_ObjectManager');
        $this->_model = new Mage_Core_Model_Layout_File_Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $theme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $file = new Mage_Core_Model_Layout_File(__FILE__, 'Fixture_Module', $theme);
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with(
                'Mage_Core_Model_Layout_File',
                $this->identicalTo(array(
                    'filename' => __FILE__,
                    'module' => 'Fixture_Module',
                    'theme' => $theme,
                ))
            )
            ->will($this->returnValue($file))
        ;
        $this->assertSame($file, $this->_model->create(__FILE__, 'Fixture_Module', $theme));
    }
}
