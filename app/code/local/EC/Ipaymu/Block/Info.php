<?php

/**
 * Description of Info
 *
 * @author ellyxc
 */
class EC_Ipaymu_Block_Info extends Mage_Payment_Block_Info {

    protected $_instructions;

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('ipaymu/info.phtml');
    }

    public function getInstructions() {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getMethod()->getInstructions();
        }
        return $this->_instructions;
    }
    
    function getStatus(){
        $statusText = array(
            -1 => Mage::helper('ipaymu')->__('Processing'),
            0 => Mage::helper('ipaymu')->__('Pending'),
            1 => Mage::helper('ipaymu')->__('Success'),
            2 => Mage::helper('ipaymu')->__('Cancelled'),
            3 => Mage::helper('ipaymu')->__('Refund'),
        );
       $info = $this->getInfo();
       /* @var $info Mage_Sales_Model_Order_Payment */
       $collection = Mage::getModel('ipaymu/transaction')
               ->getCollection();
       $collection->addFieldToFilter('order_id', $info->getOrder()->getId());
       $ipaymu = $collection->getFirstItem();
       $html = '';
       if($ipaymu->getId()){
            $html .= '<p>';
            if($ipaymu->getStatus() !== null){
                $html .= Mage::helper('ipaymu')->__('Transaction Status: %s', $statusText[$ipaymu->getStatus()]).' - ';
            }
            $html .= '<a href="'.Mage::helper("adminhtml")->getUrl('ecipaymu/adminhtml_index/status', array(
                'id' => $ipaymu->getId(),
            )).'">'.Mage::helper('ipaymu')->__('Check Status').'</a>';
            $html .= '</p>';
       }
       return $html;
    }

}