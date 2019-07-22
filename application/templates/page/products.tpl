<!-- FEATURES -->
<div
    class="home-features ng-cloak"
    ng-controller="ProductsController as products"
     >
	<div class="container" ng-if="vars.loaded">
            <div class="row">
                <div class="col-md-12">
                    <div class="feature-arrows"></div>

                    <ul class="home-features-title nav">
                        {foreach from=$categories item=cat name=categories}
                            <li {if $smarty.foreach.categories.first}class="active"{/if}><a href="#{$cat.slug}" data-toggle="tab">{$cat.name}</a></li>
                        {/foreach}
                    </ul>

                    {$products_page_features}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tab-content">
                    {foreach from=$categories item=cat name=categories}
                        <div class="tab-pane{if $smarty.foreach.categories.first} active{/if}" id="{$cat.slug}">

                            <div class="col-md-10">
                                <div class="table-box">
                                    <div class="table-responsive">
                                        <div class="pagination-block" ng-init="vars.pagination.{$cat.slug}.page=0; vars.pagination.{$cat.slug}.pages=0" ng-if="vars.pagination.{$cat.slug}.pages!=0">
                                            <div class="btn btn-primary btn-left" ng-disabled="vars.pagination.{$cat.slug}.page==0||vars.pagination.{$cat.slug}.pages==0" ng-click="vars.pagination.{$cat.slug}.page=(vars.pagination.{$cat.slug}.page>0?vars.pagination.{$cat.slug}.page-1:vars.pagination.{$cat.slug}.page)"><</div>
                                            <span>[[vars.pagination.{$cat.slug}.page+1]] / [[vars.pagination.{$cat.slug}.pages+1]]</span>
                                            <div class="btn btn-primary btn-right" ng-disabled="vars.pagination.{$cat.slug}.page==vars.pagination.{$cat.slug}.pages||vars.pagination.{$cat.slug}.pages==0" ng-click="vars.pagination.{$cat.slug}.page=(vars.pagination.{$cat.slug}.page<vars.pagination.{$cat.slug}.pages?vars.pagination.{$cat.slug}.page+1:vars.pagination.{$cat.slug}.page)">></div>
                                        </div>
                                        <table class="table table-striped table-bordered server-plans noselect">
                                            <thead>
                                                <tr>
                                                    <td ng-click="change_order('caption')"><div>{#product_name#}<span class="arrow-dummy" ng-class="{ldelim}'arrow-up':vars.order_by=='caption','arrow-down':vars.order_by=='-caption'{rdelim}"></span></div></td>
                                                    <td ng-click="change_order('cpu[0].value')"><div>{#product_cpu#}<span class="arrow-dummy" ng-class="{ldelim}'arrow-up':vars.order_by=='cpu[0].value','arrow-down':vars.order_by=='-cpu[0].value'{rdelim}"></span></div></td>
                                                    <td ng-click="change_order('ram[0].value')"><div>{#product_ram#}<span class="arrow-dummy" ng-class="{ldelim}'arrow-up':vars.order_by=='ram[0].value','arrow-down':vars.order_by=='-ram[0].value'{rdelim}"></span></div></td>
                                                    <td ng-click="change_order('hdd[0].value')"><div>{#product_hdd#}<span class="arrow-dummy" ng-class="{ldelim}'arrow-up':vars.order_by=='hdd[0].value','arrow-down':vars.order_by=='-hdd[0].value'{rdelim}"></span></div></td>
                                                    <td ng-click="change_order('band[0].value')"><div>{#product_band#}<span class="arrow-dummy" ng-class="{ldelim}'arrow-up':vars.order_by=='band[0].value','arrow-down':vars.order_by=='-band[0].value'{rdelim}"></span></div></td>
                                                    <td ng-click="change_order('location_info[0].location_code')"><div>{#product_location#}<span class="arrow-dummy" ng-class="{ldelim}'arrow-up':vars.order_by=='location_info[0].location_code','arrow-down':vars.order_by=='-location_info[0].location_code'{rdelim}"></span></div></td>
                                                    <td ng-click="change_order('complete_monthly_fee')"><div>{#product_price#}<span class="arrow-dummy" ng-class="{ldelim}'arrow-up':vars.order_by=='complete_monthly_fee','arrow-down':vars.order_by=='-complete_monthly_fee'{rdelim}"></span></div></td>
                                                    <td><div></div></td>
                                                </tr>
                                            </thead>
                                            <tbody ng-repeat="{$cat.slug}_group in data.servers.{$cat.slug}"> {* GROUPS OF THE CATEGORY *}
                                                <tr ng-repeat="{$cat.slug}_server in {$cat.slug}_group | toArray | filter_products:'{$cat.slug}':uniques.{$cat.slug}:filter_fields:vars | orderBy:natural_order:vars.order_reversed"> {* SERVERS *}
                                                    <td class="so-name"><div><span class="badge" ng-if="{$cat.slug}_server.badge!=''">[[{$cat.slug}_server.badge]]</span>[[{$cat.slug}_server.caption]]</div></td>
                                                    <td class="so-cpu">
                                                        <div class="so-container">
                                                            [[{$cat.slug}_server.cpu[0].short_name]]
                                                                <div ng-if="{$cat.slug}_server.cpu[0].upgrade==1" class="can-be-upgraded"><i class="glyphicon glyphicon-arrow-up"></i>
                                                                    <div class="can-be-upgraded-bubble">
                                                                        <ul>
                                                                            <li>{#product_available_upgrades#}</li>
                                                                            <li ng-repeat="u_prod in {$cat.slug}_server.cpu[0].upgrade_products">[[u_prod.short_name]]</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                    </td>
                                                    <td class="so-ram">
                                                        <div class="so-container">
                                                            [[{$cat.slug}_server.ram[0].short_name]]
                                                                <div ng-if="{$cat.slug}_server.ram[0].upgrade==1" class="can-be-upgraded"><i class="glyphicon glyphicon-arrow-up"></i>
                                                                    <div class="can-be-upgraded-bubble">
                                                                        <ul>
                                                                            <li>{#product_available_upgrades#}</li>
                                                                            <li ng-repeat="u_prod in {$cat.slug}_server.ram[0].upgrade_products">[[u_prod.short_name]]</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                    </td>
                                                    <td class="so-hdd">
                                                        <div class="so-container">
                                                            [[{$cat.slug}_server.hdd[0].short_name]]
                                                                <div ng-if="{$cat.slug}_server.hdd[0].upgrade==1" class="can-be-upgraded"><i class="glyphicon glyphicon-arrow-up"></i>
                                                                    <div class="can-be-upgraded-bubble">
                                                                        <ul>
                                                                            <li>{#product_available_upgrades#}</li>
                                                                            <li ng-repeat="u_prod in {$cat.slug}_server.hdd[0].upgrade_products">[[u_prod.short_name]]</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                    </td>
                                                    <td class="so-bandwidth">
                                                        <div class="so-container">
                                                            <span>
                                                                [[{$cat.slug}_server.band[0].short_name]]
                                                            </span>
                                                                <div ng-if="{$cat.slug}_server.band[0].upgrade==1" class="can-be-upgraded"><i class="glyphicon glyphicon-arrow-up"></i>
                                                                    <div class="can-be-upgraded-bubble">
                                                                        <ul>
                                                                            <li>{#product_available_upgrades#}</li>
                                                                            <li ng-repeat="u_prod in {$cat.slug}_server.band[0].upgrade_products">[[u_prod.short_name]]</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                    </td>
                                                    <td class="so-location">
                                                        <div>
                                                            <span ng-repeat="loc in {$cat.slug}_server.location_info">
                                                                [[loc.location_name]]
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="so-price"><div><strong>[[{$cat.slug}_server.complete_monthly_fee|currency]] / month</strong></div></td>
                                                    <td><div><a class="btn btn-primary so-select" href="{Helper::getFullURL("configure")}/[[{$cat.slug}_server.url]]"><i class="icon icon-arrow-right"></i></a></div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 table-box" id="product-filter-pane">
                                <div ng-repeat="(findex, fld) in filter_fields" ng-if="uniques.{$cat.slug}[findex] !== undefined"> {* FILTER FIELDS *}
                                    <h4>[[fld.desc]]</h4>
                                    <div ng-if="fld.type=='discrete'">
                                        <div ng-repeat="uniq in uniques.{$cat.slug}[findex]"> {* UNIQUE VALUES *}
                                            <input type="checkbox" ng-model="uniq.selected"/> [[uniq.val]]
                                        </div>
                                    </div>
                                    <div ng-if="fld.type=='range'">
                                        <div ng-init="max_range=uniques.{$cat.slug}[findex][0].max; min_range=uniques.{$cat.slug}[findex][0].min" filter="beautify" filter-options="[[uniques.{$cat.slug}[findex][0].suffix]]" range-slider min="0" max="[[max_range]]" model-min="uniques.{$cat.slug}[findex][0].min" model-max="uniques.{$cat.slug}[findex][0].max"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
                </div>
            </div>
            {*
            <div class="row">
                <div class="col-md-12">
                    <!-- FEATURES -->
                    <div class="home-features">
                    </div>
                    <!-- // FEATURES -->
                    {$products_page_text}
                </div>
            </div>
            *}
	</div>

        <div class="container products-features">
            {$main_page_features}
        </div>

</div>
<!-- // FEATURES -->

{include file='common/clients.tpl'}
