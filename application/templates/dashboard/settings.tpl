<div class="content-section ng-cloak" ng-controller="DashSettingsController as dash">
	<div class="container" ng-if="vars.stage_1_loaded && vars.stage_2_loaded">
		<div class="row">
                        {include file='dashboard/left_nav.tpl'}

			<div class="col-md-9">
				<div class="content-block customer-data dashboard-iheight">
					<h3 class="content-block-heading">{#db_set_customer_settings#}</h3>

                                        <form name="forms.settingsForm">

                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_company_name#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="company_name"
                                                            ng-model="user_info.company_name"
                                                            ng-init="user_info.company_name = '{$dash_settings.user_info.company}'"
                                                            ng-disabled="vars.info_status != 0"
                                                            type="text"
                                                            ng-minlength="2"
                                                            ng-maxlength="100"
                                                            ng-pattern="/^[a-zA-Z0-9\'\.\-\s]*$/"
                                                            class="form-control"
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_post_code#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="post_code"
                                                            ng-model="user_info.zip"
                                                            ng-init="user_info.zip = '{$dash_settings.user_info.zip_code}'"
                                                            ng-disabled="vars.info_status != 0"
                                                            type="text"
                                                            ng-minlength="3"
                                                            ng-maxlength="15"
                                                            required
                                                            class="form-control"
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_city#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="city"
                                                            ng-model="user_info.city"
                                                            ng-init="user_info.city = '{$dash_settings.user_info.location}'"
                                                            ng-disabled="vars.info_status != 0"
                                                            type="text"
                                                            ng-minlength="2"
                                                            ng-maxlength="100"
                                                            ng-pattern="/^[a-zA-Z\-\s]*$/"
                                                            required
                                                            class="form-control"
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_address#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="address"
                                                            ng-model="user_info.address"
                                                            ng-init="user_info.address = '{$dash_settings.user_info.address}'"
                                                            ng-disabled="vars.info_status != 0"
                                                            type="text"
                                                            ng-minlength="2"
                                                            ng-maxlength="100"
                                                            ng-pattern="/^[a-zA-Z0-9\'\.\-\s\,]*$/"
                                                            required
                                                            class="form-control"
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_country#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                                <select
                                                                    name="country"
                                                                    ng-options="country as country.label for country in vars.countries"
                                                                    ng-model="user_info.country"
                                                                    ng-init="user_info.country = find_selected_country('{$dash_settings.user_info.country}')"
                                                                    ng-change="country_prefix()"
                                                                    ng-disabled="vars.info_status != 0"
                                                                    >
                                                                </select>
                                                    </div>
                                            </div>
                                            <div class="row server-config-line" ng-show="user_info.company_name">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_vat_number#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="vat"
                                                            ng-model="user_info.vat"
                                                            ng-init="user_info.vat = '{$dash_settings.user_info.vat}'"
                                                            ng-disabled="vars.info_status != 0"
                                                            type="text"
                                                            ng-minlength="0"
                                                            ng-maxlength="20"
                                                            ng-pattern="/^[A-Z0-9]*$/"
                                                            class="form-control"
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line" ng-show="user_info.company_name">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_position#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                                <select
                                                                    name="position"
                                                                    ng-model="user_info.position"
                                                                    ng-init="user_info.position = '{$dash_settings.user_info.position}'"
                                                                    ng-disabled="vars.info_status != 0"
                                                                    >
                                                                    <option value="0">{#order_sel_pos0#}</option>
                                                                    <option value="1">{#order_sel_pos1#}</option>
                                                                    <option value="2">{#order_sel_pos2#}</option>
                                                                </select>
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_first_name#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                            <div class="row">
                                                                    <div class="col-xs-5">
                                                                        <select
                                                                            name="gender"
                                                                            ng-model="user_info.gender"
                                                                            ng-init="user_info.gender = '{$dash_settings.user_info.gender}'"
                                                                            ng-disabled="vars.info_status != 0"
                                                                            >
                                                                            <option value="0">{#order_sel_tit0#}</option>
                                                                            <option value="1">{#order_sel_tit1#}</option>
                                                                            <option value="2">{#order_sel_tit2#}</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-xs-7">
                                                                        <input
                                                                            name="first_name"
                                                                            ng-model="user_info.first_name"
                                                                            ng-init="user_info.first_name = '{$dash_settings.user_info.name}'"
                                                                            ng-disabled="vars.info_status != 0"
                                                                            type="text"
                                                                            class="form-control"
                                                                            ng-minlength="2"
                                                                            ng-maxlength="100"
                                                                            ng-pattern="/^[a-zA-Z\s]*$/"
                                                                            required
                                                                            />
                                                                    </div>
                                                            </div>
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_last_name#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="last_name"
                                                            ng-model="user_info.last_name"
                                                            ng-init="user_info.last_name = '{$dash_settings.user_info.last_name}'"
                                                            ng-disabled="vars.info_status != 0"
                                                            type="text"
                                                            class="form-control"
                                                            ng-minlength="2"
                                                            ng-maxlength="100"
                                                            ng-pattern="/^[a-zA-Z\s]*$/"
                                                            required
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_email_address#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="email_address"
                                                            type="email"
                                                            ng-model="user_info.email"
                                                            ng-init="user_info.email = '{$dash_settings.user_info.email}'"
                                                            ng-disabled="vars.info_status != 0"
                                                            ng-pattern="/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{ldelim}2,4{rdelim}$/"
                                                            class="form-control"
                                                            required
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#order_phone#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice purchase-as-phone">
                                                            <div class="row">
                                                                    <div class="col-xs-5">
                                                                        <select
                                                                            name="phone_prefix"
                                                                            ng-options="prefix as prefix.label for prefix in vars.prefixes"
                                                                            ng-model="user_info.phone_prefix"
                                                                            ng-init="user_info.phone_prefix = find_selected_phone_prefix('{$dash_settings.user_info.phone_prefix}')"
                                                                            ng-disabled="vars.info_status != 0"
                                                                            >
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-xs-7">
                                                                        <input
                                                                            name="phone"
                                                                            ng-model="user_info.phone"
                                                                            ng-init="user_info.phone = '{$dash_settings.user_info.phone_number}'"
                                                                            ng-disabled="vars.info_status != 0"
                                                                            type="text"
                                                                            class="form-control"
                                                                            ng-minlength="5"
                                                                            ng-maxlength="15"
                                                                            ng-pattern="/^[0-9]+$/"
                                                                            required
                                                                            />
                                                                    </div>
                                                            </div>
                                                    </div>
                                            </div>
                                            <div class="row server-config-line" ng-show="vars.info_changed">
                                                <div class="col-sm-4 col-md-4 server-config-text"></div>
                                                <div class="col-sm-8 col-md-8 server-config-choice">
                                                    <button
                                                        ng-class="vars.info_button"
                                                        class="btn"
                                                        ng-disabled="settingsForm.$invalid || (vars.info_status != 0)"
                                                        type="button"
                                                        ng-click="change_info()"
                                                        >
                                                        [[vars.info_button_text]]
                                                        <span
                                                            ng-show="vars.info_status == 3"
                                                            style="margin-left: 10px;"
                                                            class="glyphicon glyphicon-refresh glyphicon-refresh-animate"
                                                            >
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>

                                        </form>

                                            <hr/>

                                        <form name="passwordForm">

                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#db_set_password#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="password"
                                                            ng-model="user_pass.password"
                                                            ng-disabled="vars.password_status != 0"
                                                            type="password"
                                                            class="form-control"
                                                            ng-minlength="8"
                                                            required
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line">
                                                    <div class="col-sm-4 col-md-4 server-config-text">{#db_set_confirm_password#}:</div>
                                                    <div class="col-sm-8 col-md-8 server-config-choice">
                                                        <input
                                                            name="confirm_password"
                                                            ng-model="user_pass.confirm_password"
                                                            ng-disabled="vars.password_status != 0"
                                                            type="password"
                                                            class="form-control"
                                                            ng-minlength="8"
                                                            required
                                                            />
                                                    </div>
                                            </div>
                                            <div class="row server-config-line" ng-show="!!user_pass.password">
                                                <div class="col-sm-4 col-md-4 server-config-text"></div>
                                                <div class="col-sm-8 col-md-8 server-config-choice">
                                                    <button
                                                        ng-class="vars.password_button"
                                                        class="btn"
                                                        ng-disabled="(user_pass.password != user_pass.confirm_password) || (vars.password_status != 0)"
                                                        type="button"
                                                        ng-click="change_password()"
                                                        >
                                                        [[vars.password_button_text]]
                                                        <span
                                                            ng-show="vars.password_status == 3"
                                                            style="margin-left: 10px;"
                                                            class="glyphicon glyphicon-refresh glyphicon-refresh-animate"
                                                            >
                                                        </span>
                                                    </button>
                                                    <button
                                                        class="btn btn-primary"
                                                        ng-show="vars.password_status != 0"
                                                        style="margin-left: 10px;"
                                                        type="button"
                                                        ng-click="reset_password()"
                                                        >
                                                        {#db_set_reset#}
                                                    </button>
                                                </div>
                                            </div>

                                        </form>

					<div class="customer-data-stripes">
						<div></div>
						<div></div>
						<div></div>
						<div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
