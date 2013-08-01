<?php

/**
 * Description of IndexController
 *
 * @author ellyxc
 */
class EC_Ipaymu_IndexController extends Mage_Core_Controller_Front_Action {

    function redirectAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setIpaymuQuoteId($session->getQuoteId());
        $payment = Mage::getModel('ipaymu/payment');
        /* @var $payment EC_Ipaymu_Model_Payment */
        $url = $payment->getIpaymuPaymenUrl();
        $order = $payment->getOrder();
        if (!$order->getEmailSent()) {
            $order->sendNewOrderEmail();
            $order->setEmailSent(true);
            $order->save();
        }
        $session->unsQuoteId();
        $session->unsRedirectUrl();
        return $this->_redirectUrl($url);
    }
    
    function cancelAction(){
        $orderId = (int)$this->getRequest()->getParam('orderId');
        if($orderId){
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
                $order->cancel()->save();
                $order->sendOrderUpdateEmail();
            }
        }
        $this->_redirect('checkout/cart');
    }
    
    function successAction(){
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getIpaymuQuoteId(true));
        $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
        if ($order->getId()) {
             if(!$order->getEmailSent()){
                $order->sendNewOrderEmail();
                $order->setEmailSent(true);
                $order->save();
            }
        }
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
    
    function ipaymureturnAction(){
        $orderId = (int) $this->getRequest()->getParam('orderId');
        return $this->_redirect('sales/order/view', array('order_id' => $orderId));
    }

    function unotifyAction(){
        $orderId = (int) $this->getRequest()->getParam('orderId');
        if(!$orderId){
            return;
        }
        $data = $this->getRequest()->getPost();
        $sid = sha1(md5($orderId).$data['sid']);
        $collection = Mage::getModel('ipaymu/transaction')->getCollection();
        $collection->addFieldToFilter('sid', $sid)
                ->addFieldToFilter('order_id', $orderId);
        $ipaymu = $collection->getFirstItem();
        /* @var $ipaymu EC_Ipaymu_Model_Payment */
        if($ipaymu->getId()){
            $array = $ipaymu->toArray();

            if(isset($data['id'])){
                unset($data['id']);
            }
            if(isset($data['status'])){
                if(strtolower($data['status']) == 'pending'){
                    $data['status'] = 0;
                }elseif(strtolower($data['status']) == 'berhasil'){
                    $data['status'] = 1;
                    // update order status to processing
                    $order = Mage::getModel('sales/order')->load($orderId);
                    /* @var $order Mage_Sales_Model_Order */
                    if ($order->getId()) {
                        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage_Sales_Model_Order::STATE_PROCESSING);
                        $order->save();
                    }
                }
            }
            $data['sid'] = $sid;
            $ipaymu->setData(array_merge($array, $data));
            $ipaymu->setUpdateTime(now());
            $ipaymu->save();
        }
    }
}