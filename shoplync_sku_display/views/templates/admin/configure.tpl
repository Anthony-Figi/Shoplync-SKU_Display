{*
* 2007-2021 PrestaShop
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
*  @author    Anthony Figueroa - Shoplync Inc <sales@shoplync.com>
*  @copyright 2007-2021 Shoplync Inc
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-12 mx-auto text-center">
			<img class="mx-auto img-block w-50" src="{$module_dir|escape:'html':'UTF-8'}views/img/admin-banner.jpg" />
		</div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row text-center">
			<div class="col-xs-3"></div>
			<div class="col-xs-6">
				<p>
					<h1>{l s='Jumpstart Your Business With SMS Pro' mod='shoplync_sms_pro'}</h1>
					<ul class="ul-spaced mx-auto w-50 text-left">
						<li>{l s='Full invetory management, including automatic re-order' mod='shoplync_sms_pro'}</li>
						<li>{l s='Ability to set scaling pricing models' mod='shoplync_sms_pro'}</li>
						<li>{l s='Full Website Integration' mod='shoplync_sms_pro'}</li>
						<li>{l s='Fully Integrated Paypal Customer Verification' mod='shoplync_sms_pro'}</li>
						<li>{l s='Import/Export Parts List' mod='shoplync_sms_pro'}</li>
						<li>{l s='Built-in invoicing and order management' mod='shoplync_sms_pro'}</li>
					</ul>
				</p>

				<br />

				<p class="text-center">
					<strong>
						<a href="https://www.shoplync.com/" target="_blank" title="Copyright 2021 Shoplync. All Rights Reserved">
							{l s='© Copyright 2021 Shoplync Inc. All Rights Reserved' }
						</a>
					</strong>
				</p>
			</div>
			<div class="col-xs-3"></div>
		</div>
	</div>
</div>



<div class="panel">
	<div class="row moduleconfig-header">
        <div class="col-xs-4"></div>
        <div class="col-xs-2 mx-auto text-right">
			<img class="w-50" src="{$module_dir|escape:'html':'UTF-8'}views/img/logo.jpg" />
		</div>
		<div class="col-xs-2 text-left">
			<h2>{l s='Product SKU Display' mod='shoplync_sku_display'}</h2>
			<h4>{l s='An SMS Pro Add-On' mod='shoplync_sku_display'}</h4>
		</div>
        <div class="col-xs-4"></div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p>
					<h4>{l s='Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor' mod='shoplync_sku_display'}</h4>
					<ul class="ul-spaced">
						<li><strong>{l s='Lorem ipsum dolor sit amet' mod='shoplync_sku_display'}</strong></li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='shoplync_sku_display'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='shoplync_sku_display'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='shoplync_sku_display'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='shoplync_sku_display'}</li>
					</ul>
				</p>
            <br />
            <br />
            <hr />
			</div>
            <br>
        </div>
		<div class="row">
            <div class="col-xs-12">
            {$module_settings}
            </div>
        </div>
		<div class="row">
            <br>
            {if isset($brands) && isset($update_action) && isset($action_link) && isset($token) }
            <div class="col-xs-3"></div>
            <div class="col-xs-6">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link btn-block text-decoration-none outline-none display-5 collapsed" style="font-size:31px" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Hide MPN/SKU By Brand
                                </button>
                            </h5>
                        </div>

                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                            <br />
                            <form method="post" action="{$action_link}{$token}">
                                <input type="hidden" name="{$update_action}" value="1">
                                <button type="submit" value="1" id="module_form_submit_btn" name="{$update_action}" class="btn btn-default pull-right">
                                    <i class="process-icon-save"></i> Save List
                                </button>     
                                {include file='./_partials/brands-table.tpl' brands=$brands}
                                <button type="submit" value="1" id="module_form_submit_btn" name="{$update_action}" class="btn btn-default pull-right">
                                    <i class="process-icon-save"></i> Save List
                                </button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-3"></div>
            {/if}
		</div>
	</div>
</div>


