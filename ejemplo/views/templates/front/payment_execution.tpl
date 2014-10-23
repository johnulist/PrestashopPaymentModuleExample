{*
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
*}

{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='ejemplo'}">{l s='Checkout' mod='ejemplo'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Metodo de prueba' mod='ejemplo'}
{/capture}

<h2>{l s='Order summary' mod='ejemplo'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($nbProducts) && $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='ejemplo'}</p>
{else}

<h3>{l s='Check payment' mod='ejemplo'}</h3>
<form action="{$link->getModuleLink('ejemplo', 'validation', [], false)|escape:'html'}" method="post">
	<p>
		{l s='Your order:' mod='ejemplo'}
	</p>
	<p style="margin-top:20px;">
		- {l s='Total:' mod='ejemplo'}
		<span id="amount" class="price">{displayPrice price=$total}</span>
		{if $use_taxes == 1}
			{l s='(tax incl.)' mod='ejemplo'}
		{/if}
	</p>
	<p>
		-
		{if isset($currencies) && $currencies|@count > 1}
			{l s='We accept several currencies to receive payments by check.' mod='ejemplo'}
			<br /><br />
			{l s='Choose one of the following:' mod='ejemplo'}
			<select id="currency_payement" name="currency_payement" onchange="setCurrency($('#currency_payement').val());">
			{foreach from=$currencies item=currency}
				<option value="{$currency.id_currency}" {if isset($currencies) && $currency.id_currency == $cust_currency}selected="selected"{/if}>{$currency.name}</option>
			{/foreach}
			</select>
		{else}
			{l s='We allow the following currencies:' mod='ejemplo'}&nbsp;<b>{$currencies.0.name}</b>
			<input type="hidden" name="currency_payement" value="{$currencies.0.id_currency}" />
		{/if}
	</p>
	<p>
		{l s='Check owner and address information will be displayed on the next page.' mod='ejemplo'}
		<br /><br />
		<b>{l s='Please confirm your order by clicking \'I confirm my order\'.' mod='ejemplo'}</b>
	</p>
	<p class="cart_navigation" id="cart_navigation">
		<input type="submit" value="{l s='I confirm my order' mod='ejemplo'}" class="button btn btn-default button-medium"/>
		<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}">{l s='Other payment methods' mod='ejemplo'}</a>
	</p>
</form>
{/if}
