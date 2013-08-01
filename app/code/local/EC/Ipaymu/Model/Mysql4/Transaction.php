<?php

/**
 * Description of Transaction
 *
 * @author ellyxc
 */
class EC_Ipaymu_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('ipaymu/transaction', 'transaction_id');
    }

}