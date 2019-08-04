<?php
/**
 * Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Ecomteck.com license that is
 * available through the world-wide-web at this URL:
 * https://ecomteck.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ecomteck
 * @package     Ecomteck_OneStepCheckoutCompatible
 * @copyright   Copyright (c) 2018 Ecomteck (https://ecomteck.com/)
 * @license     https://ecomteck.com/LICENSE.txt
 */
namespace Ecomteck\OneStepCheckoutCompatible\Block\Checkout;


use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Module\Manager as ModuleManager;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Ui\Component\Form\AttributeMapper;
use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Stdlib\ArrayManager;
class OneStepCheckoutProcessor implements LayoutProcessorInterface
{
    const CONFIG_ENABLE_MAGEWORX_MULTIFEES = 'mageworx_multifees/main/enable_cart';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $moduleManager;

    /**
     * @var CheckoutSession
     */
    public $checkoutSession;

    /**
     * @var null
     */
    public $quote = null;

    /**
     * One step checkout helper
     *
     * @var \Ecomteck\OneStepCheckout\Helper\Config
     */
    protected $_config;

    protected $request;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleManager $moduleManager
     * @param CheckoutSession $checkoutSession
     * @param \Ecomteck\OneStepCheckout\Helper\Config $config
     * @param \Magento\Framework\App\Request\Http $request
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig, 
        ModuleManager $moduleManager,
        CheckoutSession $checkoutSession,
        \Ecomteck\OneStepCheckout\Helper\Config $config,
        \Magento\Framework\App\Request\Http $request,
        ArrayManager $arrayManager
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
        $this->checkoutSession = $checkoutSession;
        $this->arrayManager = $arrayManager;
        $this->_config = $config;
        $this->request = $request;
    }

    /**
     * Changes cart items to be above totals in the cart summary.
     *
     * @param array $jsLayout
     * @return array
     */
    private function modifyMageWorxMultiFees($jsLayout)
    {
        if(!$this->moduleManager->isOutputEnabled('MageWorx_MultiFees')){
            return $jsLayout;
        }
        if ($this->scopeConfig->getValue(self::CONFIG_ENABLE_MAGEWORX_MULTIFEES, ScopeInterface::SCOPE_STORE)) {
            $path = 'components/checkout/children/steps/children/shipping-step/children/shippingAddress/children/shippingAdditional/children/mageworx-shipping-fee-form-container';
            if($this->arrayManager->get($path, $jsLayout)){
                $jsLayout = $this->arrayManager->set($path.'/component', $jsLayout,'Ecomteck_OneStepCheckoutCompatible/js/MageWorx/MultiFees/view/shipping-fee');
            }
        }
        return $jsLayout;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if (!$this->_config->isEnabled()) {
            return $jsLayout;
        }

        $jsLayout = $this->modifyMageWorxMultiFees($jsLayout);
        
        return $jsLayout;
    }
}
