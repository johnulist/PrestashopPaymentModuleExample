<?php
/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = 'CREATE TABLE '._DB_PREFIX_.'payment_prueba (
		`id_payment` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`id_order` INT NOT NULL ,
		`total` FLOAT NOT NULL
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

/*TODO: 
	* Agregar un Order status que corresponda a este metodo de pago. Eg. INSERT INTO `ps_configuration`(`name`, `value`) VALUES ('PS_OS_EJEMPLO', 13)
	* Agregar el order status a la tabla ps_order_state. Eg: INSERT INTO `ps_order_state`(`id_order_state`, `invoice`, `send_email`, `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`) VALUES (13,0,0,'ejemplo','#4169E1',1,0,0,0,0,0,0)
	*/

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
