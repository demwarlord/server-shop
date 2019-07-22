<div class="content-section ng-cloak" {if !empty($dash_servers)}ng-controller="DashServersController as dash"{/if}>
    <div class="md-modal md-effect-1" id="server-reboot">
            <div class="md-content">
                    <h3>{#db_srvr_warning#}</h3>
                    <div>
                        <p>{#db_srvr_gonna_reboot_txt#}:</p>
                        <p><b>[[vars.selected_server_info[vars.selected_tab].internal_name]]</b></p>
                        <div class="button-pane">
                            <button
                                class="btn btn-warning"
                                ng-click="server_reboot()"
                                >
                                <i class="glyphicon glyphicon-refresh"></i>
                                {#db_srvr_yes_reboot#}
                            </button>
                            <button
                                class="md-close btn btn-default"
                                >
                                <i class="glyphicon glyphicon-ban-circle"></i>
                                {#db_srvr_no#}
                            </button>
                        </div>
                    </div>
            </div>
    </div>
    <div class="md-modal md-effect-1" id="server-reinstall">
            <div class="md-content">
                    <h3>{#db_srvr_warning#}</h3>
                    <div>
                        <p>{#db_srvr_gonna_reinstall_txt#}:</p>
                        <p><b>[[vars.selected_server_info[vars.selected_tab].internal_name]]</b></p>
                        {#db_srvr_reinstall_txt#}
                            {if !empty($os_list)}
                                <p>{#db_srvr_reinstall_sel_os#}:</p>
                                <div class="select-os-arch-container">
                                    <div class="select-os-container">
                                        <select
                                            id="os-selector"
                                            class="select-os selectric-with-icons"
                                            ng-model="vars.selected_os"
                                            ng-change="change_install_os()"
                                            >
                                        {foreach from=$os_list name=os_list_loop item=os}
                                            <option
                                                {if $smarty.foreach.os_list_loop.first}
                                                    {if !empty($os.x64bit)}
                                                        ng-init="vars.selected_os_caption = '{$os.caption} {$os.version}'; vars.selected_os = {$os.id}; vars.selected_os_arch = 64; vars.selected_os_x64 = {$os.x64bit}; vars.selected_os_x32 = {$os.x32bit}"
                                                    {else}
                                                        ng-init="vars.selected_os_caption = '{$os.caption} {$os.version}'; vars.selected_os = {$os.id}; vars.selected_os_arch = 32; vars.selected_os_x64 = {$os.x64bit}; vars.selected_os_x32 = {$os.x32bit}"
                                                    {/if}
                                                    selected=""
                                                {/if}
                                                value="{$os.id}"
                                                {if !empty($os.logo_picture)}
                                                    data-selectric-icon="data:image/gif;base64,{$os.logo_picture}"
                                                {/if}
                                                data-x32bit="{$os.x32bit}"
                                                data-x64bit="{$os.x64bit}"
                                                data-caption="{$os.caption} {$os.version}"
                                                >
                                                {$os.caption}
                                                {$os.version}
                                                {if !empty($os.x32bit)}
                                                    [x32]
                                                {/if}
                                                {if !empty($os.x64bit)}
                                                    [x64]
                                                {/if}
                                            </option>
                                        {/foreach}
                                        </select>
                                    </div>
                                    <div class="select-arch-container">
                                        <ul class="os-arch-selection">
                                            <li>
                                                <label>
                                                    <input type="radio" ng-model="vars.selected_os_arch" name="os_arch"  value="32" ng-disabled="!vars.selected_os_x32" />
                                                        <div class="os-arch-selection-item">
                                                            <div><img width="30" height="30" src="/img/32bit-128.png" alt=""/></div>
                                                        </div>
                                                </label>
                                            </li>
                                            <li>
                                                <label>
                                                    <input type="radio" ng-model="vars.selected_os_arch" name="os_arch" value="64" ng-disabled="!vars.selected_os_x64" />
                                                        <div class="os-arch-selection-item">
                                                            <div><img width="30" height="30" src="/img/64bit-128.png" alt=""/></div>
                                                        </div>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="selected-container">
                                        <p>{#db_srvr_you_selected_os#} <b>[[vars.selected_os_caption]] [x[[vars.selected_os_arch]]]</b></p>
                                    </div>
                                </div>
                            {/if}
                        <div class="pwd-container">
                            <form name="forms.pwdForm">
                                <hr/>
                                <div class="pass-item">
                                        <div class="pass-label">{#db_srvr_new_root_password#}:</div>
                                        <div class="pass-field">
                                            <input name="new_root_password"
                                                    ng-model="vars.new_root_password"
                                                    type="password"
                                                    ng-minlength="8"
                                                    required
                                                   />
                                        </div>
                                </div>
                                <div class="pass-item">
                                        <div class="pass-label">{#db_srvr_confirm_password#}:</div>
                                        <div class="pass-field">
                                            <input
                                                name="confirm_password"
                                                ng-model="vars.new_root_confirm_password"
                                                type="password"
                                                ng-minlength="8"
                                                compare-to="vars.new_root_password"
                                                required
                                                />
                                        </div>
                                </div>
                                <div class="pass-item">
                                        <div class="pass-label">{#db_srvr_current_user_password#}:</div>
                                        <div class="pass-field">
                                            <input
                                                name="current_password"
                                                ng-model="vars.current_password"
                                                type="password"
                                                required
                                                />
                                        </div>
                                </div>
                            </form>
                        </div>
                        <div class="button-pane">
                            <button
                                class="btn btn-danger"
                                ng-click="server_reinstall()"
                                ng-disabled="forms.pwdForm.$invalid"
                                >
                                <i class="glyphicon glyphicon-save"></i>
                                {#db_srvr_yes_reinstall#}
                            </button>
                            <button
                                class="md-close btn btn-default"
                                >
                                <i class="glyphicon glyphicon-ban-circle"></i>
                                {#db_srvr_no#}
                            </button>
                        </div>
                    </div>
            </div>
    </div>
    <div class="container md-container">
        <div class="row">
            {include file='dashboard/left_nav.tpl'}

            <div class="col-md-9">
                {if !empty($dash_servers)}
                <div class="row dash2-row">
                    <div class="col-xs-6 col-md-3">
                        <span class="dashboard-inf-block" style="background-image: url(/img/d-active-services.png);">
                            <h3>{#db_srvr_active_servers#}</h3>
                            <div class="dashboard-inf-value">[[vars.servers_count]]</div>
                            {*<span class="dashboard-inf-arrow"><i class="icon icon-arrow-right"></i></span>*}
                        </span>
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <span class="dashboard-inf-block" style="background-image: url(/img/d-network.png);">
                            <h3>{#db_srvr_network_status#}</h3>
                            <div class="dashboard-inf-value">[[vars.selected_server_info[vars.selected_tab].status.name]]</div>
                            {*<span class="dashboard-inf-arrow"><i class="icon icon-arrow-right"></i></span>*}
                        </span>
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <a href="{Helper::getFullURL("dashboard/billing/#unpaid")}" class="dashboard-inf-block" style="background-image: url(/img/d-invoices.png);">
                            <h3>{#db_srvr_due_invoices#}</h3>
                            <div class="dashboard-inf-value"><span class="red-danger">[[vars.overdue_invoices]]</span>/[[vars.due_invoices]]</div>
                            <span class="dashboard-inf-arrow"><i class="icon icon-arrow-right"></i></span>
                        </a>
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <a href="{Helper::getFullURL("dashboard/support")}" class="dashboard-inf-block" style="background-image: url(/img/d-tickets.png);">
                            <h3>{#db_srvr_open_tickets#}</h3>
                            <div class="dashboard-inf-value text-muted">[[vars.tickets_count]]</div>
                            <span class="dashboard-inf-arrow"><i class="icon icon-arrow-right"></i></span>
                        </a>
                    </div>
                </div>

                <div class="big-chart-block">
                    <header>
                        <ul class="nav nav-tabs">
                            {if !empty($dash_servers)}
                                {$active=0}
                                {foreach from=$dash_servers item=group}
                                    {if $group.type.code == 0} {* dedicated *}
                                        <li{if !$active} class="active" ng-init="vars.selected_tab={$group.type.code}"{$active=1}{/if}>
                                            <a href="#dedicated" data-toggle="tab" ng-click="change_tab({$group.type.code})">{#db_srvr_dedicated_servers#}</a>
                                    {elseif $group.type.code == 1} {* virtual *}
                                        <li{if !$active} class="active" ng-init="vars.selected_tab={$group.type.code}"{$active=1}{/if}>
                                            <a href="#virtual" data-toggle="tab" ng-click="change_tab({$group.type.code})">{#db_srvr_virtual_servers#}</a>
                                    {else}
                                        <li{if !$active} class="active" ng-init="vars.selected_tab={$group.type.code}"{$active=1}{/if}>
                                            <a href="#servers" data-toggle="tab" ng-click="change_tab({$group.type.code})">{#db_srvr_servers#}</a>
                                    {/if}
                                        </li>
                                {/foreach}
                            {/if}
                        </ul>
                    </header>
                    <div class="big-chart-content">
                        <div class="tab-content">
                            {if !empty($dash_servers)}
                                {$active=0}
                                {foreach from=$dash_servers item=group}
                                    {if $group.type.code == 0} {* dedicated *}
                                        <div class="tab-pane {if !$active}active{$active=1}{/if}" id="dedicated">
                                    {elseif $group.type.code == 1} {* virtual *}
                                        <div class="tab-pane {if !$active}active{$active=1}{/if}" id="virtual">
                                    {else}
                                        <div class="tab-pane {if !$active}active{$active=1}{/if}" id="servers">
                                    {/if}
                                            <select
                                                ng-change="get_server_info({$group.type.code})"
                                                ng-init="set_selected_server({$group.type.code},'{$group.items[0].server.id}')"
                                                ng-model="vars.selected_server[{$group.type.code}]"
                                                >
                                                {foreach from=$group.items item=server name=select}
                                                    <option
                                                        value="{$server.server.id}"
                                                        {if $smarty.foreach.select.index == 0}selected=""{/if}
                                                        >
                                                        {$server.server.internal_name}
                                                    </option>
                                                {/foreach}
                                            </select>

                                            <div role="tabpanel" id="server-charts">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li role="presentation" class="active"><a href="#server-manage" data-toggle="tab">{#db_srvr_manage#}</a></li>
                                                    <li role="presentation"><a href="#traffic-usage" data-toggle="tab">{#db_srvr_traffic_usage#}</a></li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="server-manage">
                                                        <div class="button-pane">
                                                            <button
                                                                class="btn btn-warning md-trigger"
                                                                data-modal="server-reboot"
                                                                >
                                                                <i class="glyphicon glyphicon-refresh"></i>
                                                                {#db_srvr_reboot#}
                                                            </button>
                                                            <button
                                                                class="btn btn-danger md-trigger"
                                                                data-modal="server-reinstall"
                                                                >
                                                                <i class="glyphicon glyphicon-save"></i>
                                                                {#db_srvr_reinstall#}
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="traffic-usage">
                                                        <div ng-bind-html="trafficUsage({$group.type.code})">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>

                <div class="row dash2-row">
                    <div class="col-xs-6 col-md-3">
                        <div class="small-chart-block">
                            <h6 class="small-chart-heading">{#db_srvr_monthly_visitors#}</h6>
                            {#db_srvr_chart#}
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <div class="small-chart-block">
                            <h6 class="small-chart-heading">{#db_srvr_cloud_storage_usage#}</h6>
                            {#db_srvr_chart#}
                            <div class="small-chart-bottom-text"><a href="#">{#db_srvr_show_my_files#}</a></div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <div class="small-chart-block">
                            <h6 class="small-chart-heading">{#db_srvr_virtual_machines#}</h6>
                            {#db_srvr_chart#}
                            <div class="small-chart-bottom-text"><a href="#">{#db_srvr_report_log#}</a></div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <div class="small-chart-block">
                            <h6 class="small-chart-heading">{#db_srvr_ram_usage#}</h6>
                            {#db_srvr_chart#}
                            <div class="small-chart-bottom-text">28765 / 32768 {#mb#}</div>
                        </div>
                    </div>
                </div>
                {else}
                    <div class="big-chart-block">
                        <div class="big-chart-content">
                            <div class="row">
                                <div class="col-xs-9 col-sm-9">
                                    <h4><i style="font-size: 40px; vertical-align: middle; margin: 0 20px; color: #777;" class="glyphicon glyphicon-info-sign"></i> You have no released servers yet</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
<div class="md-overlay"></div>
</div>
