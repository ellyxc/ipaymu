<?php

/**
 * Description of Form
 *
 * @author ellyxc
 */
class EC_Ipaymu_Block_Form extends Mage_Payment_Block_Form {

    protected $_instructions;

    protected function _construct() {
        $html = '<a href="https://ipaymu.com/about-us/" target="_blank">' . Mage::helper('ipaymu')->__('What is ipaymu?') . '</a>';
        $this->setTemplate('ipaymu/form.phtml');
        $this->setMethodLabelAfterHtml($html);
        parent::_construct();
    }

    public function getInstructions() {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getMethod()->getInstructions();
        }
        return $this->_instructions;
    }

}