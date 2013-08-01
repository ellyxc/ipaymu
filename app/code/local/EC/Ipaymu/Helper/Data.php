<?php

/**
 * Description of Data
 *
 * @author ellyxc
 */
class EC_Ipaymu_Helper_Data extends Mage_Core_Helper_Abstract {

    function getRedirectUrl($orderId, $appId = null, $cancel = true) {
        if($orderId instanceof Mage_Sales_Model_Order){
            $order = $orderId;
        }else{
            $order = Mage::getModel('sales/order')->load($orderId);
        }
        if(!$appId){
            $payment = Mage::getModel('ipaymu/payment');
            $appId = $payment->getConfigData('app_key');
        }
        $url = $this->getIpaymuUrl().'/payment.htm';
        $httpClient = new Zend_Http_Client($url, array(
                    'adapter' => 'Zend_Http_Client_Adapter_Curl'
                ));
        try {
            $httpClient->setParameterPost(array(
                'key' => $appId,
                'action' => 'payment',
                'product' => Mage::helper('ipaymu')->__('Sales Order #%s', $order->getIncrementId()),
                'price' => $this->getIpaymuAmount($order),
                'quantity' => 1,
                'format' => 'json',
                'ureturn' => $cancel ? Mage::getUrl('ipaymu/index/success') :Mage::getUrl('ipaymu/index/ipaymureturn', array('orderId' => $order->getId())),
                'ucancel' => $cancel ? Mage::getUrl('ipaymu/index/cancel', array('orderId' => $order->getId())) : Mage::getUrl('ipaymu/index/cancel'),
                'unotify' => Mage::getUrl('ipaymu/index/unotify', array('orderId' => $order->getId()))
            ));
            $httpClient->request(Zend_Http_Client::POST);
            $result = Zend_Json::decode($httpClient->getLastResponse()->getBody());
            if(isset($result['url'])){
                $this->_saveIpaymuDetail($result['sessionID'], $order->getId());
                return $result['url'];
            }else{
                throw new Exception($result['Keterangan'], $result['Status']);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    
    protected function _saveIpaymuDetail($sid, $orderId){
        $sid = sha1(md5($orderId).$sid);
        $collection = Mage::getModel('ipaymu/transaction')->getCollection();
        $collection->addFieldToFilter('sid', $sid);
//                ->addFieldToFilter('order_id', $orderId);
        $ipaymu = $collection->getFirstItem();
        if(!$ipaymu->getId()){
            $model = Mage::getModel('ipaymu/transaction');
            $model->setOrderId($orderId);
            $model->setSid($sid);
            $model->setCreatedTime(now());
            $model->setUpdateTime(now());
            $model->save();
        }
    }
    
    function getIpaymuAmount(Mage_Sales_Model_Order $order){
        $amount = $order->getBaseGrandTotal();
        if($order->getBaseCurrencyCode() != EC_Ipaymu_Model_Payment::IPAYMU_CURRENCY){
            if($order->getOrderCurrencyCode() == EC_Ipaymu_Model_Payment::IPAYMU_CURRENCY){
                $amount =  $order->getGrandTotal();
            }else{
                throw new Mage_Core_Exception(Mage::helper('ipaymu')->__('Invalid order currency'));
//                $amount = Mage::helper('directory')->currencyConvert($amount, $order->getBaseCurrencyCode(),EC_Ipaymu_Model_Payment::IPAYMU_CURRENCY);
            }
        }
        $payment = Mage::getModel('ipaymu/payment');
        if($payment->getConfigData('percent_fee')){
            $fee = $amount * $payment->getConfigData('percent_fee') / 100;
            $amount = $amount + $fee;
        }
        if($payment->getConfigData('fix_fee')){
            $amount = $amount + $payment->getConfigData('fix_fee');
        }
        return $amount;
    }
    
    private function getIpaymuUrl(){
        return 'https://my.ipaymu.com';
    }

}