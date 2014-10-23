<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.6.0
 */
 
 //controlador
class EjemploPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	//public $display_column_left = false;

	public function initContent()
	{
		parent::initContent();//llama al init() de FrontController, que es la clase padre

		$cart = $this->context->cart;
		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');

		//asigno las variables que se van a a ver en la template de payment (payment.tpl)
		$this->context->smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),//productos
			'cust_currency' => $cart->id_currency,//moneda en la que paga el cliente
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),//moneda del modulo
			'total' => $cart->getOrderTotal(true, Cart::BOTH),//total de la orden
			'this_path' => $this->module->getPathUri(),
			'this_path_ejemplo' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$this->setTemplate('payment_execution.tpl');
	}
}
