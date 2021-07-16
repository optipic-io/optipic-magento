<?php
namespace Optipic\Optipic\Block\Adminhtml\Form\Field;

//use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
//use Magento\Framework\DataObject;
//use Magento\Framework\Exception\LocalizedException;
//use Vendor\Module\Block\Adminhtml\Form\Field\TaxColumn;
//use Magento\Framework\Data\Form\Element\AbstractElement;

use Magento\Backend\Block\AbstractBlock;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class Statscript
 */
class Statscript  extends \Magento\Backend\Block\AbstractBlock implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $moduleInfo =  $objectManager->get('Magento\Framework\Module\ModuleList')->getOne('Optipic_Optipic');
        $moduleVersion = $moduleInfo['setup_version'];
        
        $optipic = $objectManager->get('Optipic\Optipic\Model\Optipic');
        $settings = $optipic->getSettings();
        $siteId = $settings['site_id'];
        
        //$currentUrl = Mage::helper('core/url')->getCurrentUrl();
        //$url = Mage::getSingleton('core/url')->parseUrl($currentUrl);
        //$domain = $objectManager->get('Magento\Framework\Url')->getCurrentUrl();
        //$domain = $objectManager->get('Zend\Uri\Http')->parse($domain);//->getQueryAsArray();
        
        return sprintf('<script src="https://optipic.io/api/cp/stat?domain=%s&sid=%s&cms=magento&stype=cdn&append_to=%s&version=%s"></script>',
            urlencode($_SERVER["HTTP_HOST"]),
            urlencode($siteId),
            urlencode(".section-config:first"),
            urlencode($moduleVersion)
        );
    }
}