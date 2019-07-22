<div class="content-section ng-cloak" ng-controller="DashBillingController as dash">
	<div class="container" ng-if="check_loaded()">
		<div class="row">
                        {include file='dashboard/left_nav.tpl'}

                        <div class="col-md-9">

                            <div class="big-chart-block">

                                    <header>
                                            <ul class="nav nav-tabs">
                                                    <li ng-class="{ldelim}active: is_active('#unpaid'){rdelim}"><a href="#tab_unpaid" data-toggle="tab" ng-click="switch_tab('#unpaid')">{#db_tab_unpaid_documents#}</a></li>
                                                    <li ng-class="{ldelim}active: is_active('#history'){rdelim}"><a href="#tab_history" data-toggle="tab" ng-click="switch_tab('#history')">{#db_tab_billing_history#}</a></li>
                                            </ul>
                                    </header>

                                <div class="big-chart-content">
                                    <div class="tab-content">
                                            <div class="tab-pane" id="tab_unpaid" ng-class="{ldelim}active: is_active('#unpaid'){rdelim}">

                                                    <div class="content-block customer-data">
                                                        <h3 class="content-block-heading">{#db_hdr_unpaid_documents#}</h3>

                                                        <div class="row" ng-show="isEmpty(documents)">
                                                            <div class="col-xs-9 col-sm-9">
                                                                <h4>{#db_no_unpaid_documents#}</h4>
                                                            </div>
                                                        </div>

                                                        <form name="forms.unpaidDocumentsForm" ng-show="!isEmpty(documents)">
                                                            <table class="table table-bordered dashboard-table billing-table">
                                                                    <tr>
                                                                        <th>{#db_tbl_document#}</th>
                                                                        <th>{#db_tbl_date#}</th>
                                                                        <th>{#db_tbl_status#}</th>
                                                                        <th>{#db_tbl_due_days#}</th>
                                                                        <th>{#db_tbl_amount#}</th>
                                                                        <th>{#db_tbl_still_due#}</th>
                                                                        <th></th>
                                                                    </tr>
                                                                    <tr ng-repeat="doc in documents" ng-class="{ldelim}danger:(doc.diff > 20){rdelim}">
                                                                        <td>
                                                                            <a target="_blank" href="[[doc.document_url]]" tooltip-html-unsafe="[[doc.tooltip]]"><i class="glyphicon glyphicon-file"></i></a>
                                                                            [[doc.type]] N[[doc.idInvoice]]
                                                                        </td>
                                                                        <td>[[doc.dtDate * 1000 | date:'dd/MM/yyyy HH:mm']]</td>
                                                                        <td>
                                                                            <i
                                                                                ng-if="(doc.saferpayResult == '0' && doc.saferpaySettled == '0')"
                                                                                style="color : orange;"
                                                                                class="glyphicon glyphicon-time"
                                                                                tooltip="{#db_tbl_pending#}"
                                                                                >
                                                                            </i>
                                                                            <i
                                                                                ng-if="(doc.saferpayResult != '0' || doc.saferpaySettled != '0')"
                                                                                ng-style="(doc.diff > 19 ? {ldelim}color:'red'{rdelim} : (doc.diff > 13 ? {ldelim}color:'orange'{rdelim} : {ldelim}color:'green'{rdelim}))"
                                                                                class="glyphicon glyphicon-ok-sign"
                                                                                >
                                                                            </i>
                                                                        </td>
                                                                        <td>[[(doc.diff <= 0 ? (Math.abs(doc.diff)) : 'overdue (' + doc.diff + ' day' + (doc.diff > 1 ? 's' : '') + ')')]]</td>
                                                                        <td>[[doc.total | number:2]]</td>
                                                                        <td>[[(doc.total - doc.linked) | number:2]]</td>
                                                                        <td>
                                                                            <input
                                                                                type="checkbox"
                                                                                ng-disabled="(doc.saferpayResult == '0' && doc.saferpaySettled == '0')"
                                                                                ng-change="select_documents()"
                                                                                ng-model="doc.selected"
                                                                                />
                                                                        </td>
                                                                    </tr>
                                                            </table>
                                                        </form>
                                                    </div>


                                                    <div class="order-payment-method">
                                                            <h3 class="order-payment-method-heading">{#db_pay_by#}</h3>

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
                                                                                    <input type="radio" ng-model="vars.payment_method" name="payment-method" value="alertpay"/>
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

                                                            {#db_pay_by_txt#}
                                                    </div>

                                                    <div class="content-block">
                                                        <div class="row">
                                                            <div class="col-xs-3 col-sm-3">
                                                                <h4>{#db_enter_bonus#}:</h4>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-3">
                                                                <input
                                                                    name="bonus"
                                                                    ng-model="vars.sum_bonus_txt"
                                                                    type="number"
                                                                    ng-pattern="/^[0-9\.\,]*$/"
                                                                    class="form-control"
                                                                    />
                                                            </div>
                                                            <div class="col-xs-3 col-sm-3">
                                                            </div>
                                                            <div class="col-xs-3 col-sm-3">

                                                            </div>
                                                        </div>

                                                        <hr/>

                                                        <div class="row">
                                                            <div class="col-xs-3 col-sm-3" ng-show="vars.sum_selected > 0">
                                                                <h4>{#db_bil_selected#}</h4>
                                                                <span>$ [[vars.sum_selected | number:2]]</span>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-3">
                                                                <h4>{#db_bil_bonus#}</h4>
                                                                <span>$ [[vars.sum_bonus | number:2]]</span>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-3">
                                                                <h4>{#db_bil_total#}</h4>
                                                                <span>$ [[vars.sum_total | number:2]]</span>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-3">

                                                                            <button
                                                                                ng-class="vars.pay_button"
                                                                                class="btn-lg pay-button"
                                                                                type="button"
                                                                                ng-click="pay()"
                                                                                ng-disabled="vars.sum_total == 0"
                                                                                >
                                                                                [[vars.pay_button_text]]
                                                                                <span
                                                                                    ng-show="vars.pay_status == 3"
                                                                                    style="margin-left: 10px;"
                                                                                    class="glyphicon glyphicon-refresh glyphicon-refresh-animate"
                                                                                    >
                                                                                </span>
                                                                            </button>

                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="tab-pane" id="tab_history" ng-class="{ldelim}active: is_active('#history'){rdelim}">

                                                    <div class="content-block customer-data">
                                                        <h3 class="content-block-heading">{#db_hdr_billing_history#}</h3>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                    <h4>{#db_bil_select_start_date#}: </h4>
                                                                    <p class="input-group">
                                                                      <input type="text" class="form-control" datepicker-popup="[[format]]" ng-click = "$parent.opened_from = true" ng-model="vars.dt_from" max-date="vars.max_date" is-open="$parent.opened_from" datepicker-options="dateOptions" ng-required="true" close-text="{#db_bil_datepicker_close#}" />
                                                                      <span class="input-group-btn">
                                                                        <button type="button" class="btn btn-default" ng-click="open_from($event)"><i class="glyphicon glyphicon-calendar"></i></button>
                                                                      </span>
                                                                    </p>
                                                            </div>

                                                            <div class="col-md-4">
                                                                    <h4>{#db_bil_select_end_date#}: </h4>
                                                                    <p class="input-group">
                                                                      <input type="text" class="form-control" datepicker-popup="[[format]]" ng-click = "$parent.opened_to = true" ng-model="vars.dt_to" max-date="vars.max_date" is-open="$parent.opened_to" datepicker-options="dateOptions" ng-required="true" close-text="{#db_bil_datepicker_close#}" />
                                                                      <span class="input-group-btn">
                                                                        <button type="button" class="btn btn-default" ng-click="open_to($event)"><i class="glyphicon glyphicon-calendar"></i></button>
                                                                      </span>
                                                                    </p>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <span class="dummy"></span>
                                                                    <button
                                                                        ng-class="vars.filter_button"
                                                                        class="btn apply-filter"
                                                                        type="button"
                                                                        ng-click="apply_filter()"
                                                                        ng-disabled="
                                                                            vars.dt_from == null ||
                                                                            vars.dt_from == '' ||
                                                                            vars.dt_to == null ||
                                                                            vars.dt_to == '' ||
                                                                            vars.dt_to > vars.max_date ||
                                                                            vars.dt_from > vars.max_date ||
                                                                            vars.dt_to < vars.dt_from "
                                                                        >
                                                                        [[vars.filter_button_text]]
                                                                        <span
                                                                            ng-show="vars.filter_status == 3"
                                                                            style="margin-left: 10px;"
                                                                            class="glyphicon glyphicon-refresh glyphicon-refresh-animate"
                                                                            >
                                                                        </span>
                                                                    </button>
                                                            </div>

                                                        </div>

                                                        <div class="row" ng-show="isEmpty(history)">
                                                            <div class="col-xs-9 col-sm-9">
                                                                <h4>{#db_bil_no_records#}</h4>
                                                            </div>
                                                        </div>

                                                        <form name="forms.historyForm" ng-show="!isEmpty(history)">
                                                            <table class="table table-bordered dashboard-table billing-table">
                                                                    <tr>
                                                                        <th>{#db_tbl_hist_date#}</th>
                                                                        <th>{#db_tbl_hist_caption#}</th>
                                                                        <th>{#db_tbl_hist_debit#}</th>
                                                                        <th>{#db_tbl_hist_credit#}</th>
                                                                        <th>{#db_tbl_hist_documents#}</th>
                                                                    </tr>
                                                                    <tr ng-repeat="rec in history">
                                                                        <td>[[rec.date * 1000 | date:'dd/MM/yyyy HH:mm']]</td>
                                                                        <td ng-bind-html="rec.caption | unsafe"></td>
                                                                        <td>[[rec.debit | number:2]]</td>
                                                                        <td>[[rec.credit | number:2]]</td>
                                                                        <td ng-if="rec.document_id !== undefined"><a target="_blank" href="[[rec.document_url]]"><i class="glyphicon glyphicon-file"></i></a></td>
                                                                        <td ng-if="rec.document_id === undefined"></td>
                                                                    </tr>
                                                            </table>
                                                        </form>
                                                    </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
		</div>
	</div>
</div>
