<?php

/**
 * @author ellyxc
 */
$installer = $this;

$installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('ipaymu/transaction')};
    CREATE TABLE {$this->getTable('ipaymu/transaction')}(
    `transaction_id` int NOT NULL AUTO_INCREMENT,
    `sid` varchar(255) NULL DEFAULT NULL,
    `trx_id` varchar(255) NULL DEFAULT NULL,
    `merchant` varchar(255) NULL DEFAULT NULL,
    `buyer` varchar(255) NULL DEFAULT NULL,
    `order_id` int NULL DEFAULT NULL,
    `total` double DEFAULT 0,
    `comments` text null default null,
    `referer` varchar(255) NULL DEFAULT NULL,
    `status` tinyint(1) NULL DEFAULT NULL,
    `created_time` datetime NULL DEFAULT NULL,
    `update_time` datetime NULL DEFAULT NULL,
    PRIMARY KEY (`transaction_id`)
    )
    ENGINE=InnoDB
    CHARACTER SET utf8 COLLATE utf8_general_ci;
    ");