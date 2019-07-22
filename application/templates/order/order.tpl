{if $order_payment_status || $order_payment_finalize}
    {if $order_payment_status eq 1}
        <div class="normal-block" ng-controller="WaitController as wait">
            <div id="checkout-payment-process">
                {#order_payment_in_process#}
            </div>
            <div id="checkout-payment-process-cancel">
                <a href="{Helper::getFullURL("cancel-payment")}">{#order_cancel_payment#}</a>
            </div>
        </div>
    {elseif $order_payment_finalize}
        <div class="normal-block">
            <div id="checkout-payment-process">
                {#order_thank_you#}
            </div>
        </div>
    {/if}
{else}
<!-- PURCHASE AS -->
<div class="normal-block ng-cloak" ng-controller="OrderController as order" {if empty($user_profile)}data-user-logged="0"{else}data-user-logged="1" data-user-id="{$user_profile.id}"{/if}>
	<div class="container" ng-if="vars.stage_1_loaded && vars.stage_2_loaded && vars.stage_3_loaded">

		<div class="row">
			<div class="col-md-8">
				<div class="content-block purchase-as">
                                    {if empty($user_profile)}
					<div class="purchase-as-in">
						<h3 class="content-block-heading">{#order_purchase_as#}</h3>

						<div class="clearfix">
							<ul class="nav nav-tabs">
								<li class="active"><a ng-click="change_status(1)" href="#business" data-toggle="tab">{#order_business#}</a></li>
								<li><a ng-click="change_status(0)" href="#personal" data-toggle="tab">{#order_personal#}</a></li>
							</ul>
						</div>
                                                <div class="row">
                                                    <div class="login-info">
                                                        {#order_if_registered#}
                                                    </div>
                                                </div>

						<div class="tab-content">
							<div class="tab-pane active" id="business">

                                                            <form name="regForm_business">
								<div class="form-inf">{#order_required_fields#}</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_company_name#} *:</div>
                                                                        <div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="business_company_name"
                                                                                ng-model="reg.business.company_name"
                                                                                type="text"
                                                                                ng-minlength="2"
                                                                                ng-maxlength="100"
                                                                                required
                                                                                class="form-control"
                                                                                ng-change="update_userinfo()"
                                                                                ng-model-options="{ debounce: 1000 }"
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_post_code#} *:</div>
                                                                        <div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="business_post_code"
                                                                                ng-model="reg.post_code"
                                                                                type="text"
                                                                                ng-minlength="3"
                                                                                ng-maxlength="15"
                                                                                required
                                                                                class="form-control"
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_city#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="business_city"
                                                                                ng-model="reg.city"
                                                                                type="text"
                                                                                ng-minlength="2"
                                                                                ng-maxlength="100"
                                                                                required
                                                                                class="form-control"
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_address#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="business_company_address"
                                                                                ng-model="reg.address"
                                                                                type="text"
                                                                                ng-minlength="2"
                                                                                ng-maxlength="100"
                                                                                required
                                                                                class="form-control"
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_country#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                                    <select
                                                                                        name="business_country"
                                                                                        ng-options="country as country.label for country in vars.countries"
                                                                                        ng-model="reg.country"
                                                                                        ng-change="country_prefix()"
                                                                                        >
                                                                                    </select>
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_vat_number#}:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="business_vat"
                                                                                ng-model="reg.business.vat"
                                                                                type="text"
                                                                                ng-minlength="0"
                                                                                ng-maxlength="12"
                                                                                class="form-control"
                                                                                ng-change="update_userinfo()"
                                                                                ng-model-options="{ debounce: 1000 }"
                                                                                />
                                                                        </div>
								</div>

                                                                <hr/>

								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_position#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                                    <select
                                                                                        name="business_position"
                                                                                        ng-model="reg.business.position"
                                                                                        >
                                                                                        <option value="0">{#order_sel_pos0#}</option>
                                                                                        <option value="1">{#order_sel_pos1#}</option>
                                                                                        <option value="2">{#order_sel_pos2#}</option>
                                                                                    </select>
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_first_name#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
										<div class="row">
											<div class="col-xs-5">
                                                                                            <select
                                                                                                name="business_gender"
                                                                                                ng-model="reg.gender"
                                                                                                >
                                                                                                <option value="0">{#order_sel_tit0#}</option>
                                                                                                <option value="1">{#order_sel_tit1#}</option>
                                                                                                <option value="2">{#order_sel_tit2#}</option>
                                                                                            </select>
                                                                                        </div>
											<div class="col-xs-7">
                                                                                            <input
                                                                                                name="business_first_name"
                                                                                                ng-model="reg.first_name"
                                                                                                type="text"
                                                                                                class="form-control"
                                                                                                ng-minlength="2"
                                                                                                ng-maxlength="100"
                                                                                                required
                                                                                                />
                                                                                        </div>
										</div>
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_last_name#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="business_last_name"
                                                                                ng-model="reg.last_name"
                                                                                type="text"
                                                                                class="form-control"
                                                                                ng-minlength="2"
                                                                                ng-maxlength="100"
                                                                                required
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_email_address#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
										<div class="row">
											<div class="col-xs-8">
                                                                                            <input
                                                                                                name="business_email_address"
                                                                                                type="email"
                                                                                                ng-disabled="vars.email_validated || vars.email_in_validation"
                                                                                                ng-model="reg.email_address"
                                                                                                class="form-control"
                                                                                                required
                                                                                                />
                                                                                        </div>
											<div class="col-xs-4">
                                                                                            <button ng-show="!vars.email_validated && !vars.email_in_validation" class="btn btn-primary validate-button" type="button" ng-click="validate_email()">{#order_activate#}</button>
                                                                                            <button ng-show="vars.email_in_validation" class="btn btn-primary validate-button" type="button" ng-click="validate_code()">{#order_check_code#}</button>
                                                                                            <button ng-show="vars.email_validated" class="btn btn-success" type="button">{#order_validated#}</button>
                                                                                        </div>
										</div>
                                                                        </div>
								</div>
								<div class="row server-config-line" ng-show="vars.email_in_validation">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_validation_code#} :</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="business_email_validation_code"
                                                                                ng-model="vars.email_validation_code"
                                                                                type="text"
                                                                                class="form-control"
                                                                                ng-minlength="10"
                                                                                ng-maxlength="10"
                                                                                required
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_phone#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice purchase-as-phone">
										<div class="row">
											<div class="col-xs-5">
                                                                                            <select
                                                                                                name="business_phone_prefix"
                                                                                                ng-options="prefix as prefix.label for prefix in vars.prefixes"
                                                                                                ng-model="reg.phone_prefix"
                                                                                                >
                                                                                            </select>
                                                                                        </div>
											<div class="col-xs-7">
                                                                                            <input
                                                                                                name="business_phone"
                                                                                                ng-model="reg.phone"
                                                                                                type="text"
                                                                                                class="form-control"
                                                                                                ng-minlength="3"
                                                                                                ng-maxlength="15"
                                                                                                ng-pattern="/^[0-9]+$/"
                                                                                                required
                                                                                                />
                                                                                        </div>
										</div>
									</div>
								</div>
                                                            </form>
                                                        </div>
        						<div class="tab-pane" id="personal">
                                                            <form name="regForm_personal">
								<div class="form-inf">{#order_required_fields#}</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_post_code#} *:</div>
                                                                        <div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="personal_post_code"
                                                                                ng-model="reg.post_code"
                                                                                type="text"
                                                                                ng-minlength="3"
                                                                                ng-maxlength="15"
                                                                                required
                                                                                class="form-control"
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_city#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="personal_city"
                                                                                ng-model="reg.city"
                                                                                type="text"
                                                                                ng-minlength="2"
                                                                                ng-maxlength="100"
                                                                                required
                                                                                class="form-control"
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_address#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="personal_address"
                                                                                ng-model="reg.address"
                                                                                type="text"
                                                                                class="form-control"
                                                                                ng-minlength="2"
                                                                                ng-maxlength="100"
                                                                                required
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_country#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                                    <select
                                                                                        name="personal_country"
                                                                                        ng-model="reg.country"
                                                                                        ng-options="country as country.label for country in vars.countries"
                                                                                        ng-change="country_prefix()"
                                                                                        >
                                                                                    </select>
                                                                        </div>
								</div>

                                                                <hr/>

								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_first_name#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
										<div class="row">
											<div class="col-xs-5">
                                                                                            <select
                                                                                                name="personal_gender"
                                                                                                ng-model="reg.gender"
                                                                                                >
                                                                                                <option value="0">{#order_sel_tit0#}</option>
                                                                                                <option value="1">{#order_sel_tit1#}</option>
                                                                                                <option value="2">{#order_sel_tit2#}</option>
                                                                                            </select>
                                                                                        </div>
											<div class="col-xs-7">
                                                                                            <input
                                                                                                name="personal_first_name"
                                                                                                ng-model="reg.first_name"
                                                                                                type="text"
                                                                                                ng-minlength="2"
                                                                                                ng-maxlength="100"
                                                                                                required
                                                                                                class="form-control"
                                                                                                />
                                                                                        </div>
										</div>
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_last_name#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="personal_last_name"
                                                                                ng-model="reg.last_name"
                                                                                type="text"
                                                                                ng-minlength="2"
                                                                                ng-maxlength="100"
                                                                                required
                                                                                class="form-control"
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_email_address#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
										<div class="row">
											<div class="col-xs-8">
                                                                                            <input
                                                                                                name="personal_email_address"
                                                                                                type="email"
                                                                                                ng-disabled="vars.email_validated || vars.email_in_validation"
                                                                                                ng-model="reg.email_address"
                                                                                                class="form-control"
                                                                                                required
                                                                                                />
                                                                                        </div>
											<div class="col-xs-4">
                                                                                            <button ng-show="!vars.email_validated && !vars.email_in_validation" class="btn btn-primary validate-button" type="button" ng-click="validate_email()">{#order_activate#}</button>
                                                                                            <button ng-show="vars.email_in_validation" class="btn btn-primary validate-button" type="button" ng-click="validate_code()">{#order_check_code#}</button>
                                                                                            <button ng-show="vars.email_validated" class="btn btn-success" type="button">{#order_validated#}</button>
                                                                                        </div>
										</div>
                                                                        </div>
								</div>
								<div class="row server-config-line" ng-show="vars.email_in_validation">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_validation_code#} :</div>
									<div class="col-sm-8 col-md-6 server-config-choice">
                                                                            <input
                                                                                name="personal_email_validation_code"
                                                                                ng-model="vars.email_validation_code"
                                                                                type="text"
                                                                                class="form-control"
                                                                                ng-minlength="10"
                                                                                ng-maxlength="10"
                                                                                required
                                                                                />
                                                                        </div>
								</div>
								<div class="row server-config-line">
									<div class="col-sm-4 col-md-6 server-config-text">{#order_phone#} *:</div>
									<div class="col-sm-8 col-md-6 server-config-choice purchase-as-phone">
										<div class="row">
											<div class="col-xs-5">
                                                                                            <select
                                                                                                name="personal_phone_prefix"
                                                                                                ng-model="reg.phone_prefix"
                                                                                                ng-options="prefix as prefix.label for prefix in vars.prefixes"
                                                                                                >
                                                                                            </select>
                                                                                        </div>
											<div class="col-xs-7">
                                                                                            <input
                                                                                                name="personal_phone"
                                                                                                ng-model="reg.phone"
                                                                                                type="text"
                                                                                                class="form-control"
                                                                                                ng-minlength="3"
                                                                                                ng-maxlength="15"
                                                                                                ng-pattern="/^[0-9]+$/"
                                                                                                required
                                                                                                />
                                                                                        </div>
										</div>
									</div>
								</div>
                                                            </form>
							</div>
						</div>
					</div>
                                    {else}

					<div class="purchase-as-in">
						<h3 class="content-block-heading">{#order_you_logged_as#}</h3>

                                                <div id="user-info">
                                                    {if !empty($user_profile.info.company)}
                                                    <div class="user-info-line">
                                                        <b>{#order_company_name#}:</b> {$user_profile.info.company}
                                                    </div>
                                                    {/if}
                                                    {if !empty($user_profile.info.vat)}
                                                    <div class="user-info-line">
                                                        <b>{#order_vat_number#}:</b> {$user_profile.info.vat}
                                                    </div>
                                                    {/if}
                                                    <div class="user-info-line">
                                                        <b>{#order_name#}:</b> {$user_profile.first_name} {$user_profile.last_name}
                                                    </div>
                                                    <div class="user-info-line">
                                                        <b>{#order_email_address#}:</b> {$user_profile.email}
                                                    </div>
                                                    <div class="user-info-line">
                                                        <b>{#order_address#}:</b> {$user_profile.info.address}
                                                    </div>
                                                    <div class="user-info-line">
                                                        <b>{#order_city#}:</b> {$user_profile.info.location}
                                                    </div>
                                                    <div class="user-info-line">
                                                        <b>{#order_country#}:</b> {$user_profile.info.country_name}
                                                    </div>
                                                    <div class="user-info-line">
                                                        <b>{#order_post_code#}:</b> {$user_profile.info.zip_code}
                                                    </div>
                                                    <div class="user-info-line">
                                                        <b>{#order_phone#}:</b> ({$user_profile.info.phone_prefix}) {$user_profile.info.phone_number}
                                                    </div>
                                                </div>
					</div>


                                    {/if}
					<div class="order-payment-method">
						<h3 class="order-payment-method-heading">{#order_payment_method#}</h3>

						<ul class="order-payment-method-selection">
							<li>
								<label>
									<input type="radio" ng-model="vars.payment_method" name="payment-method"  value="visa" checked/>
									<div class="order-payment-method-item">
										<div>
											<div><img src="/img/pm-visa.png" alt=""/></div>
										</div>
									</div>
								</label>
							</li>
							<li>
								<label>
									<input type="radio" ng-model="vars.payment_method" name="payment-method" value="mastercard"/>
									<div class="order-payment-method-item">
										<div>
											<div><img src="/img/pm-mastercard.png" alt=""/></div>
										</div>
									</div>
								</label>
							</li>
							<li>
								<label>
									<input type="radio" ng-model="vars.payment_method" name="payment-method" value="paypal"/>
									<div class="order-payment-method-item">
										<div>
											<div><img src="/img/pm-paypal.png" alt=""/></div>
										</div>
									</div>
								</label>
							</li>
                                                        <li>
                                                                <label>
                                                                        <input type="radio" ng-model="vars.payment_method" name="payment-method" value="payza"/>
                                                                        <div class="order-payment-method-item">
                                                                                <div>
                                                                                        <div><img src="/img/pm-payza.png" alt=""/></div>
                                                                                </div>
                                                                        </div>
                                                                </label>
                                                        </li>
                                                        <li>
                                                                <label>
                                                                        <input type="radio" ng-model="vars.payment_method" name="payment-method" value="webmoney"/>
                                                                        <div class="order-payment-method-item">
                                                                                <div>
                                                                                        <div><img src="/img/pm-wm.png" alt=""/></div>
                                                                                </div>
                                                                        </div>
                                                                </label>
                                                        </li>
						</ul>
                                                {#order_payment_txt#}
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="content-block your-server">
					<div class="your-server-content">
						<h3 class="content-block-heading">{#order_summary#}</h3>

						<div class="your-server-subtitle"><span>{#order_included#}</span></div>
						<ul class="server-features">
                                                    <li ng-repeat="item in cart" class="row server-config-line">
                                                        [[item.caption]]
                                                        <span class="pull-right">
                                                            $
                                                            [[(item.complete_monthly_fee*vars.selected_period+item.complete_setup_fee) | number:2]]
                                                            x
                                                            <span class="item-quantity">[[item.quantity]]</span>
                                                            <sup>*</sup>
                                                        </span>
                                                    </li>
						</ul>
                                                <div class="remark"><sup>*</sup> {#order_price_without_discounts#}</div>
					</div>
					<div class="payment-period">
						<h3 class="payment-period-heading">{#order_payment_period#}</h3>

						<div class="payment-periods-box">
							<div class="payment-item" ng-class="{ldelim}'is-active':(period.period == vars.selected_period){rdelim}" ng-repeat="period in periods" ng-if="period.period >= vars.min_period">
                                                                <div class="payment-container">
                                                                        <div class="payment-col payment-checkbox">
                                                                            <label>
                                                                                <input type="radio" ng-model="vars.selected_period" name="payment-period" ng-checked="period.period == vars.selected_period" value="[[period.period]]" />
                                                                                <span></span>
                                                                            </label>
                                                                        </div>
                                                                        <div class="payment-container" style="border: none;" ng-bind-html="myHTMLinPeriods(period)"></div>
                                                                </div>
                                                        </div>
						</div>
					</div>
					<div class="order-summary">
						<div class="order-summary-total"><span>$ [[sums.total_sum_vat | number:2]]</span>{#order_total#}</div>
						<div class="order-summary-payment-period" ng-show="vars.user_vat_rate > 0">{#order_including_vat#} ([[vars.user_vat_rate]]%) $[[sums.vat | number:2]]</div>
						<div class="order-summary-btns clearfix">
							<a ng-href="{Helper::getFullURL("cart")}" class="order-summary-edit">{#order_edit#}</a>
							<a ng-click="pay_order(vars.user_logged ? 0 : (reg.status == 1 ? regForm_business : regForm_personal))" href="" class="order-summary-purchase">{#order_purchase#}</a>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
<!-- // PURCHASE AS -->
{/if}