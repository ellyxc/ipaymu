<?php

/**
 * Description of Transaction
 *
 * @author ellyxc
 */
class EC_Ipaymu_Model_Transaction extends Mage_Core_Model_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('ipaymu/transaction');
    }

}