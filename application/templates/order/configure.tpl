<!-- CONFIGURE -->
<div
    ng-controller="ConfiguratorController as cnf"
    class="normal-block ng-cloak"
    data-configured="{$server_configured}"
    data-url="{$server_url}"
    {if !empty($server_configured)}
        data-cart-item-id="{$cart_item_id}"
    {/if}
    >
    <div class="container" ng-if="vars.loaded">
		<div class="row">
			<div class="col-md-8">
				<div class="content-block configure-server">
					<h3 class="content-block-heading">{#cnf_configure_your_server#}</h3>
					<div ng-repeat="cat in srv.categories" class="row server-config-line">
                                            <div class="row server-config-line">
                                                <div class="col-md-3 col-md-offset-2">[[cat.name]]:</div>
                                            </div>
                                            <div ng-repeat="subcat in srv_products | isInCategory:cat.id" class="row server-config-line server-choice-config-line">
                                                    <div class="col-sm-4 col-md-6 server-config-text">[[subcat.name]]:</div>
                                                    <div class="col-sm-8 col-md-6 server-config-choice">
                                                        <div class="selectric-wrapper">
                                                            <select
                                                                ng-options="prod as formatting_prods(prod) for prod in subcat.products | crossFilter:subcat.id:srv.cross_dep:selected_products:this"
                                                                ng-model="selected_prod"
                                                                ng-init="selected_prod = find_selected(subcat.products)"
                                                                ng-change="replace_product(selected_prod)">
                                                            </select>
                                                        </div>
                                                        <div ng-if="selected_prod.info!=''" class="additional-info">
                                                            <i class="glyphicon glyphicon-question-sign"></i>
                                                            <div class="additional-info-bubble">
                                                                <span class="additional-info-info">[[selected_prod.info]]</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <hr>
                                        </div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text">{#cnf_server_location#}:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <select
                                                        ng-options="loc as loc.location_name for loc in locations"
                                                        ng-model="vars.location"
                                                        >
                                                    </select>
                                                </div>
					</div>
					<div class="row server-config-line" ng-show="!vars.comment_toggle">
						<div class="col-sm-12 col-md-12 server-config-text">
                                                    <button
                                                        class="btn btn-primary"
                                                        type="button"
                                                        ng-click="vars.comment_toggle=true"
                                                        >
                                                        {#cnf_btn_add_comment#}
                                                    </button>
                                                </div>
					</div>
					<div class="row server-config-line" ng-show="vars.comment_toggle">
						<div class="col-sm-4 col-md-6 server-config-text">{#cnf_server_comment#}:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <textarea
                                                        style="width: 100%; min-height: 150px; resize: vertical; padding: 10px;"
                                                        ng-model="vars.comment"
                                                        >
                                                    </textarea>
                                                </div>
					</div>
					<div class="row server-config-line" ng-show="vars.comment_toggle">
						<div class="col-sm-12 col-md-12 server-config-text">
                                                    <button
                                                        class="btn btn-warning"
                                                        type="button"
                                                        ng-click="vars.comment='';vars.comment_toggle=false"
                                                        >
                                                        {#cnf_btn_remove_comment#}
                                                    </button>
                                                </div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="content-block your-server">
					<div class="your-server-content">
						<h3 class="content-block-heading">{#cnf_your_server#}</h3>
                                                <h5 class="content-block-heading">[[srv.caption]]</h5>

						<div class="your-server-subtitle"><span>{#cnf_included#}</span></div>
						<ul class="server-features">
                                                    <li ng-repeat="prod in selected_products | isDefaultProduct:1" class="row server-config-line"><span ng-bind="prod.label" class="prod-label"></span> : <span ng-bind="prod.name" class="prod-caption"></span></li>
						</ul>

						<div class="your-server-subtitle"><span>{#cnf_additional#}</span></div>
						<ul class="server-features">
                                                    <li ng-repeat="prod in selected_products | isDefaultProduct:0" ng-bind-html="myHTMLinAdditional(prod)"></li>
						</ul>
					</div>
				</div>
                                <div class="payment-period">
                                        <h3 class="quantity-heading">{#cnf_quantity#}</h3>

                                        <div class="quantity-box noselect">
                                            <i class="glyphicon glyphicon-minus-sign" ng-click="change_count(-1)"></i>
                                                <input type="text" ng-model="vars.quantity"/>
                                            <i class="glyphicon glyphicon-plus-sign" ng-click="change_count(1)"></i>
                                        </div>

                                        <h3 class="payment-period-heading">{#cnf_total#}</h3>

                                        <div class="payment-periods-box">
                                                <div class="payment-item" ng-if="(period.period == vars.min_period)" ng-repeat="period in periods">
                                                        <div class="payment-container">
                                                                <div class="payment-container" style="border: none;" ng-bind-html="myHTMLinPeriods(period)"></div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="config-purchase" style="margin-top: 70px;">
                                    <a ng-click="add_to_cart()" href="">{if empty($server_configured)}{#cnf_add_to_cart#}{else}{#cnf_save_to_cart#}{/if}</a>
                                </div>

			</div>
		</div>

	</div>
</div>
<!-- // CONFIGURE -->
