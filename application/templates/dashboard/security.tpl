<div class="content-section ng-cloak" ng-controller="DashSecurityController as dash">
	<div class="container" ng-if="vars.stage_1_loaded">
		<div class="row">
                        {include file='dashboard/left_nav.tpl'}

			<div class="col-md-9">
				<div class="content-block customer-data dashboard-iheight">
					<h3 class="content-block-heading">{#db_security_hdr#}</h3>

                                        <div class="row" ng-show="isEmpty(user_logins)">
                                            <div class="col-xs-9 col-sm-9">
                                                <h4>{#db_security_no_records#}</h4>
                                            </div>
                                        </div>

                                        <form name="forms.userLoginsForm" ng-show="!isEmpty(user_logins)">
                                            <div class="pagination-block user-logins-pagination">
                                                <button
                                                    class="btn btn-primary"
                                                    ng-disabled="pagination.page <= 0"
                                                    type="button"
                                                    ng-click="pagination.page = (pagination.page > 0 ? pagination.page - 1 : 0); get_user_logins_page()"
                                                    >
                                                    <
                                                </button>

                                                <button
                                                    class="btn btn-primary"
                                                    ng-disabled="(pagination.page+1)*pagination.count>pagination.total"
                                                    type="button"
                                                    ng-click="pagination.page = (((pagination.page+1)*pagination.count < pagination.total) ? pagination.page + 1 : pagination.page); get_user_logins_page()"
                                                    >
                                                    >
                                                </button>
                                            </div>
                                            <table class="table table-bordered dashboard-table user-logins-table">
                                                    <tr>
                                                        <th style="width: 19%;">{#db_security_tbl_date#}</th>
                                                        <th style="width: 15%;">{#db_security_tbl_ip#}</th>
                                                        <th>{#db_security_tbl_ua#}</th>
                                                    </tr>
                                                    <tr ng-repeat="ul in user_logins">
                                                        <td>[[ul.date * 1000 | date:'dd/MM/yyyy HH:mm']]</td>
                                                        <td>[[ul.ip]]</td>
                                                        <td>[[ul.browser]]</td>
                                                    </tr>
                                            </table>
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
