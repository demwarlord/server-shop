<div class="content-section ng-cloak" ng-controller="DashOverviewController as dash">
    <div class="container">
        <div class="row">
            {include file='dashboard/left_nav.tpl'}

            <div class="col-md-5">
                <div class="content-block customer-data dashboard-iheight">
                    <h3 class="content-block-heading">{#db_ovvw_customer_data#}</h3>
                    <div class="btn-group btn-group-justified btn-group-lg customer-data-inf">
                        <a href="{Helper::getFullURL("dashboard/servers")}" class="btn btn-default">{#db_ovvw_your_servers#}: {$dash_overview.servers_count}</a>
                        <a href="{Helper::getFullURL("dashboard/support")}" class="btn btn-default">{#db_ovvw_your_tickets#}: {$dash_overview.tickets_count}</a>
                    </div>

                    <table class="dashboard-table">
                        <tr>
                            <th>{#db_ovvw_customer_number#}:</th>
                            <td>{$dash_overview.user_info.id}</td>
                        </tr>
                        <tr>
                            <th>{#db_ovvw_full_name#}:</th>
                            <td>{$dash_overview.user_info.name} {$dash_overview.user_info.last_name}</td>
                        </tr>
                        <tr>
                            <th>{#db_ovvw_e_mail#}:</th>
                            <td>{$dash_overview.user_info.email}</td>
                        </tr>
                        <tr>
                            <th>{#db_ovvw_last_login#}:</th>
                            <td>{if empty($dash_overview.last_login)}{#db_ovvw_not_logged#}{else}{$dash_overview.last_login.date|date_format:"%d/%m/%Y - %H:%M"}{/if}</td>
                        </tr>
                        <tr>
                            <th>{#db_ovvw_ip#}:</th>
                            <td>{if empty($dash_overview.last_login)}{#db_ovvw_not_logged#}{else}{$dash_overview.last_login.ip}{/if}</td>
                        </tr>
                        <tr>
                            <th>{#db_ovvw_sms_notification_lng#}:</th>
                            <td>
                                <select
                                    ng-change="change_user_info()"
                                    ng-model="user_info.language"
                                    ng-disabled="user_info.disabled"
                                    ng-init="user_info.language = '{$dash_overview.user_info.language}'"
                                >
                                    {foreach from=$dash_overview.supported_languages key=key item=lang}
                                    <option
                                        value="{$key}"
                                        {if $dash_overview.user_info.language == $key}selected=""{/if}
                                    >
                                        {$lang.caption}
                                    </option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>{#db_ovvw_sms_notification#}:</th>
                            <td>
                                <input
                                    type="checkbox"
                                    ng-change="change_user_info()"
                                    ng-model="user_info.sms_notify"
                                    ng-disabled="user_info.disabled"
                                    ng-init="user_info.sms_notify = {if !empty($dash_overview.user_info.sms_notify)}true{else}false{/if}"
                                    {if !empty($dash_overview.user_info.sms_notify)}checked=""{/if}
                                />
                            </td>
                        </tr>
                    </table>

                    <div class="customer-data-stripes">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-block financial-data dashboard-iheight">
                    <a class="financial-pay" href="{Helper::getFullURL("dashboard/billing/#unpaid")}">
                        <span>+</span>
                    </a>

                    <h3 class="content-block-heading">{#db_ovvw_financial_data#}</h3>

                    <div class="account-balance">
                        <div class="account-balance-val">$ {$dash_overview.user_balance|string_format:'%.2f'}</div>
                        <div class="account-balance-descr">{#db_ovvw_account_balance#}</div>
                    </div>

                    <table class="dashboard-table">
                        <tr>
                            <th>{#db_ovvw_last_invoice#}:</th>
                            <td>{if !empty($dash_overview.last_invoice)}{$dash_overview.last_invoice.date|date_format:"%d/%m/%Y - %H:%M"}{else}{#db_ovvw_no_paid_documents#}{/if}</td>
                        </tr>
                        <tr>
                            <th>{#db_ovvw_amount#}:</th>
                            <td>{if !empty($dash_overview.last_invoice)}$ {$dash_overview.last_invoice.total|string_format:'%.2f'}{else}{#db_ovvw_no_paid_documents#}{/if}</td>
                        </tr>
                    </table>
                    <hr/>
                    <table class="dashboard-table">
                        <tr>
                            <th>{#db_ovvw_next_invoice#}:</th>
                            <td>{if !empty($dash_overview.next_invoice)}{$dash_overview.next_invoice.date|date_format:"%d/%m/%Y - %H:%M"}{else}{#db_ovvw_no_unpaid_documents#}{/if}</td>
                        </tr>
                        <tr>
                            <th>{#db_ovvw_amount#}:</th>
                            <td>{if !empty($dash_overview.next_invoice)}$ {$dash_overview.next_invoice.total|string_format:'%.2f'}{else}{#db_ovvw_no_unpaid_documents#}{/if}</td>
                        </tr>
                    </table>
                    <hr/>
                    <table class="dashboard-table">
                        <tr>
                            <th>{#db_ovvw_auto_pay#}:</th>
                            <td>
                                <input
                                    type="checkbox"
                                    ng-change="change_user_info()"
                                    ng-model="user_info.auto_pay"
                                    ng-disabled="user_info.disabled"
                                    ng-init="user_info.auto_pay = {if !empty($dash_overview.user_info.auto_pay)}true{else}false{/if}"
                                    {if !empty($dash_overview.user_info.auto_pay)}checked=""{/if}
                                />
                            </td>
                        </tr>
                    </table>

                    <div class="clearfix financial-data-btns">
                        <div class="financial-data-pay-now"><a class="btn btn-type2" href="{Helper::getFullURL("dashboard/billing/#unpaid")}">{#db_ovvw_pay_now#}</a></div>
                        <div class="invoice-history-link"><a href="{Helper::getFullURL("dashboard/billing/#history")}">{#db_ovvw_invoice_history#}</a></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>