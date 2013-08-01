<?php

/**
 * Description of Collection
 *
 * @author ellyxc
 */
class EC_Ipaymu_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('ipaymu/transaction');
    }

}