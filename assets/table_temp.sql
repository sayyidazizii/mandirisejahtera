/*Table structure for table `acct_savings_account_detail_temp` */

DROP TABLE IF EXISTS `acct_savings_account_detail_temp`;

CREATE TABLE `acct_savings_account_detail_temp` (
  `savings_account_detail_temp_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) NOT NULL DEFAULT '0',
  `savings_account_id` bigint(22) DEFAULT '0',
  `savings_id` int(10) DEFAULT '0',
  `mutation_id` int(10) DEFAULT '0',
  `member_id` bigint(22) DEFAULT '0',
  `today_transaction_date` date DEFAULT NULL,
  `yesterday_transaction_date` date DEFAULT NULL,
  `transaction_code` varchar(100) DEFAULT '',
  `opening_balance` decimal(20,2) DEFAULT '0.00',
  `mutation_in` decimal(20,2) DEFAULT '0.00',
  `mutation_out` decimal(20,2) DEFAULT '0.00',
  `last_balance` decimal(20,2) DEFAULT '0.00',
  `daily_average_balance` decimal(20,2) DEFAULT '0.00',
  `description` text,
  `operated_name` varchar(50) NOT NULL DEFAULT '',
  `savings_print_status` int(1) NOT NULL DEFAULT '0',
  `created_id` int(18) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`savings_account_detail_temp_id`),
  KEY `FK_acct_savings_account_detail_temp_savings_account_id` (`savings_account_id`),
  KEY `FK_acct_savings_account_detail_temp_savings_id` (`savings_id`),
  KEY `FK_acct_savings_account_detail_temp_mutation_id` (`mutation_id`),
  KEY `FK_acct_savings_account_detail_temp_member_id` (`member_id`),
  CONSTRAINT `FK_acct_savings_account_detail_temp_member_id` FOREIGN KEY (`member_id`) REFERENCES `core_member` (`member_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_acct_savings_account_detail_temp_savings_account_id` FOREIGN KEY (`savings_account_id`) REFERENCES `acct_savings_account` (`savings_account_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_acct_savings_account_detail_temp_savings_id` FOREIGN KEY (`savings_id`) REFERENCES `acct_savings` (`savings_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `acct_savings_profit_sharing_temp` */

DROP TABLE IF EXISTS `acct_savings_profit_sharing_temp`;

CREATE TABLE `acct_savings_profit_sharing_temp` (
  `savings_profit_sharing_temp_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `savings_profit_sharing_temp_log_id` bigint(22) DEFAULT '0',
  `branch_id` int(10) NOT NULL DEFAULT '0',
  `savings_account_id` bigint(22) DEFAULT '0',
  `savings_id` int(10) DEFAULT '0',
  `member_id` bigint(22) DEFAULT '0',
  `savings_profit_sharing_temp_date` date DEFAULT NULL,
  `savings_index_amount` decimal(10,5) DEFAULT '0.00000',
  `savings_daily_average_balance_minimum` decimal(20,2) DEFAULT '0.00',
  `savings_daily_average_balance` decimal(20,2) DEFAULT '0.00',
  `savings_profit_sharing_temp_amount` decimal(20,2) DEFAULT '0.00',
  `savings_interest_temp_amount` decimal(20,2) DEFAULT '0.00',
  `savings_tax_temp_amount` decimal(20,2) DEFAULT '0.00',
  `savings_account_last_balance` decimal(20,2) DEFAULT '0.00',
  `savings_profit_sharing_temp_period` varchar(10) DEFAULT NULL,
  `savings_profit_sharing_temp_token` varchar(100) DEFAULT NULL,
  `operated_name` varchar(50) NOT NULL,
  `created_id` int(10) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`savings_profit_sharing_temp_id`),
  UNIQUE KEY `savings_profit_sharing_temp_token` (`savings_profit_sharing_temp_token`),
  KEY `FK_acct_savings_profit_sharing_temp_savings_account_id` (`savings_account_id`),
  CONSTRAINT `FK_acct_savings_profit_sharing_temp_savings_account_id` FOREIGN KEY (`savings_account_id`) REFERENCES `acct_savings_account` (`savings_account_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*Table structure for table `acct_savings_account_temp` */

DROP TABLE IF EXISTS `acct_savings_account_temp`;

CREATE TABLE `acct_savings_account_temp` (
  `savings_account_temp_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) DEFAULT '0',
  `savings_id` int(10) DEFAULT '0',
  `savings_account_id` bigint(22) DEFAULT '0',
  `savings_account_daily_average_balance` decimal(20,2) DEFAULT '0.00',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`savings_account_temp_id`),
  KEY `savings_account_id` (`savings_account_id`),
  KEY `savings_id` (`savings_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `FK_acct_savings_account_temp_savings_account_id` FOREIGN KEY (`savings_account_id`) REFERENCES `acct_savings_account` (`savings_account_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8