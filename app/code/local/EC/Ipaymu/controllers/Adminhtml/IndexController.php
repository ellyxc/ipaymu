<?php

/**
 * Description of IndexController
 *
 * @author ellyxc
 */
class EC_Ipaymu_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    function statusAction() {
        $id = (int) $this->getRequest()->getParam('id');
        if(!$id){
            return $this->_redirect('adminhtml/sales_order/index');
        }
        $ipaymu = Mage::getModel('ipaymu/transaction')->load($id);
        $payment = Mage::getModel('ipaymu/payment');
        $appId = $payment->getConfigData('app_key');
        $content = file_get_contents('https://my.ipaymu.com/api/CekTransaksi.php?key='.$appId.'&id='.$ipaymu->getTrxId().'&format=json');
        $result = json_decode($content, true);
        if(isset($result['Status'])){
            $status = $result['Status'];
            if($status >= -1 && $status <= 3){
                $ipaymu->setStatus($status);
                $ipaymu->save();
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ipaymu')->__('Ipaymu Transaction Status: "%s"', $result['Keterangan']));
        return $this->_redirect('adminhtml/sales_order/view', array(
            'order_id' =>  $ipaymu->getOrderId()
        ));
    }

}