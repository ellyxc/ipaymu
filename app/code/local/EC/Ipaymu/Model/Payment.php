<?php

/**
 * Description of Payment
 *
 * @author ellyxc
 */
class EC_Ipaymu_Model_Payment extends Mage_Payment_Model_Method_Abstract{
    
    const IPAYMU_CURRENCY = 'IDR';
    
    protected $_code = 'ipaymu';
    protected $_formBlockType = 'ipaymu/form';
    protected $_infoBlockType = 'ipaymu/info';
    protected $_isInitializeNeeded = true;
    protected $_canUseForMultishipping = false;
    protected $_canManageRecurringProfiles = false;
    protected $_canUseInternal = false;
    
    protected $_ipaymu_order_increment_id;
    protected $_ipaymu_order;

    function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('ipaymu/index/redirect', array('_secure' => true));
    }

    public function getInstructions() {
        return $this->getConfigData('instructions');
    }
    
    public function getIpaymuPaymenUrl(){
        return Mage::helper('ipaymu')
                ->getRedirectUrl($this->getOrder(),$this->getConfigData('app_key'));
    }


    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }
    
    function getOrderIncrementId(){
        if(!$this->_ipaymu_order_increment_id){
            $this->_ipaymu_order_increment_id = $this->getCheckout()->getLastRealOrderId();
        }
        return $this->_ipaymu_order_increment_id;
    }
    
    function setOrderIncrementId($incrementId){
        $this->_ipaymu_order_increment_id = $incrementId;
        return $this;
    }

    /**
     * 
     * @return Mage_Sales_Model_Order
     */
    function getOrder() {
        if (!$this->_ipaymu_order) {
            $orderIncrementId = $this->getOrderIncrementId();
            $this->_ipaymu_order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        }
        return $this->_ipaymu_order;
    }

   function getIpaymuAmount() {
        return Mage::helper('ipaymu')->getIpaymuAmount($this->getOrder());
    }
    
    public function canUseForCurrency($currencyCode) {
        return self::IPAYMU_CURRENCY == $currencyCode;
    }
}
