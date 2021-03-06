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
 * @package     Mage_Theme
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Files uploader block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Uploader extends Mage_Adminhtml_Block_Media_Uploader
{
    /**
     * Path to uploader template
     *
     * @var string
     */
    protected $_template = 'browser/content/uploader.phtml';

    /**
     * Prepare layout
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
     */
    protected function _prepareLayout()
    {
        $this->getConfig()->setUrl(
            $this->getUrl('*/*/upload', $this->helper('Mage_Theme_Helper_Storage')->getRequestParams())
        );
        return parent::_prepareLayout();
    }

    /**
     * Return storage helper
     *
     * @return Mage_Theme_Helper_Storage
     */
    public function getHelperStorage()
    {
        return $this->helper('Mage_Theme_Helper_Storage');
    }
}
