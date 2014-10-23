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
class EjemploValidationModuleFrontController extends ModuleFrontController
{
	//valida que todo este bien
	public function postProcess()
	{
		$cart = $this->context->cart;//recupero el carrito

		//si no hay un cliente registrado, o una direccion de entrega, o direccion de contacto o el modulo no esta activo
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');//redirecciona al primer paso

		// Verifica que la opcion de pago este disponible
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'ejemplo')
			{
				$authorized = true;
				break;
			}

		if (!$authorized)//si no esta disponible la opcion de pago
			die($this->module->l('This payment method is not available.', 'validation'));//avisa

		$customer = new Customer($cart->id_customer);//recupera al objeto cliente

		if (!Validate::isLoadedObject($customer))//si no hay un cliente
			Tools::redirect('index.php?controller=order&step=1');//redirecciona al primer paso

		$currency = $this->context->currency;//recupero la moneda de la compra
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);//recupero el total de la compra

		/* VERIFICACION DE LA ORDEN.
			Los parametros enviados a la funcion validateOrder son:
				* id del carrito
				* Order Status correspondiente a este metodo de pago (sacado de la tabla ps_configuration): 'PS_OS_EJEMPLO'=13
				* monto total de la orden
				* metodo de pago / nombre del modulo
				* mensaje : null
				* variables extra: null
				* moneda en la que se hace el pago
				* dont_touch_amount
				* secure_key del cliente
		*/
		
		$this->module->validateOrder(	(int)$cart->id,
										Configuration::get('PS_OS_EJEMPLO'),
										$total,
										$this->module->displayName,
										NULL,
										NULL,
										(int)$currency->id,
										false,
										$customer->secure_key);
		Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
		/*redirigimos a OrderConfirmationController con los siguientes parametros:
				* id_cart: id del carrito
				* id_module: id del modulo que estamos usando
				* id_order: id de la orden
				* key : secure_key del cliente
		*/
	}
}
