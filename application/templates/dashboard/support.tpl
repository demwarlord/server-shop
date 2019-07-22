<div class="content-section ng-cloak" ng-controller="DashSupportController as dash">
    <div class="container">
        <div class="row">
            {include file='dashboard/left_nav.tpl'}

            <div class="col-md-9">

                <div class="big-chart-block">

                        <header>
                                <ul class="nav nav-tabs">
                                        <li class="active"><a href="#ticket-system" data-toggle="tab">{#db_tick_ticket_system#}</a></li>
                                </ul>
                        </header>

                    <div class="big-chart-content">
                        <div class="tab-content">
                                <div class="tab-pane active" id="ticket-system">

                                        <div class="content-block customer-data" ng-if="!loaded">
                                            <preloader></preloader>
                                        </div>

                                        <div class="content-block customer-data" ng-if="loaded">
                                            <h3 class="content-block-heading">{#db_tick_tickets#}</h3>
                                            <div>
                                                <div class="btn-group btn-group-justified btn-group-lg customer-data-inf tickets-nav">
                                                    <button class="btn btn-default" ng-click="switchTicketView('open')" ng-class="{ldelim}active:vars.open{rdelim}">{#db_tick_open_tickets#}</button>
                                                    <button class="btn btn-default" ng-click="switchTicketView('pending')" ng-class="{ldelim}active:vars.pending{rdelim}">{#db_tick_pending_tickets#}</button>
                                                    <button class="btn btn-default" ng-click="switchTicketView('closed')" ng-class="{ldelim}active:vars.closed{rdelim}">{#db_tick_closed_tickets#}</button>
                                                </div>
                                                <div class="row" ng-if="vars.open">
                                                    <div class="clearfix" ng-if="(vars.tickets.open).length !== 0">
                                                        <div class="col-md-2">
                                                            <h6>{#db_tick_ticket_no#}</h6>
                                                            <button class="btn btn-default btn-block" ng-repeat="ticket in vars.tickets.open" ng-click="switchCurrentTicket(ticket)" ng-class="{ldelim}active:vars.currentTicket.ticket_id == ticket.ticket_id{rdelim}">[[ticket.ticket_id]]</button>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <div class="btn btn-default ticket-ctrl" ng-click="closeTicket('open')">{#db_tick_close_ticket#}</div>
                                                            <div class="btn btn-default ticket-ctrl">{#db_tick_upload_file#}</div>
                                                            <div class="btn btn-default ticket-ctrl" ng-click="answerTicket()">{#db_tick_answer_ticket#}</div>
                                                            <div class="clearfix" ng-if="vars.answer">
                                                                <textarea style="width: 100%; margin: 15px 0;" ng-model="vars.answerMessage"></textarea>
                                                                <button style="float: right;" class="btn btn-default" ng-click="submitAnswerTicket('open')">{#db_tick_submit_answer#}</button>
                                                            </div>
                                                            <div style="width: 100%; margin: 15px 0;" ng-if="(vars.currentTicket).length !== 0">
                                                                <div class="clearfix" ng-if="vars.currentTicket.server_id != 0">
                                                                    <h6><span>{#db_tick_concerned_server#}:</span> <span>[[vars.currentTicket.server_name]]</span></h6>
                                                                </div>
                                                                <p>{#db_tick_messages#}:</p>
                                                                <div>
                                                                    <div class="ticket-message" ng-repeat="message in vars.currentTicket.messages">
                                                                        <div class="clearfix" ng-if="message.response == 1"><i class="icon icon-life-buoy" style="color: #f66b4f; float: left;"></i><p style="float: right;">[[message.date * 1000 | date:'yyyy-MM-dd HH:mm']]</p></div>
                                                                        <div class="clearfix" ng-if="message.response == 0"><i class="icon icon-life-buoy" style="color: #4591d7; float: left;"></i><p style="float: right;">[[message.date * 1000 | date:'yyyy-MM-dd HH:mm']]</p></div>
                                                                        <div><p>[[message.message]]</p></div>
                                                                        <div class="ticket-stripes" ng-class="message.response == 0 ? 'blue' : 'red'"></div>
                                                                        <hr>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 ticket-none" ng-if="(vars.tickets.open).length === 0">
                                                        <h6>{#db_tick_no_open_tickets#}</h6>
                                                    </div>
                                                </div>

                                                <div class="row" ng-if="vars.pending">
                                                    <div class="clearfix" ng-if="(vars.tickets.pending).length !== 0">
                                                        <div class="col-md-2">
                                                            <h6>{#db_tick_ticket_no#}</h6>
                                                            <button class="btn btn-default btn-block" ng-repeat="ticket in vars.tickets.pending" ng-click="switchCurrentTicket(ticket)" ng-class="{ldelim}active:vars.currentTicket.ticket_id == ticket.ticket_id{rdelim}">[[ticket.ticket_id]]</button>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <div class="btn btn-default ticket-ctrl" ng-click="closeTicket('pending')">{#db_tick_close_ticket#}</div>
                                                            <div class="btn btn-default ticket-ctrl">{#db_tick_upload_file#}</div>
                                                            <div class="btn btn-default ticket-ctrl" ng-click="answerTicket()">{#db_tick_answer_ticket#}</div>
                                                            <div class="clearfix" ng-if="vars.answer">
                                                                <textarea style="width: 100%; margin: 15px 0;" ng-model="vars.answerMessage"></textarea>
                                                                <button style="float: right;" class="btn btn-default" ng-click="submitAnswerTicket('pending')">{#db_tick_submit_answer#}</button>
                                                            </div>
                                                            <div style="width: 100%; margin: 15px 0;" ng-if="(vars.currentTicket).length !== 0">
                                                                <div class="clearfix" ng-if="vars.currentTicket.server_id != 0">
                                                                    <h6><span>{#db_tick_concerned_server#}:</span> <span>[[vars.currentTicket.server_name]]</span></h6>
                                                                </div>
                                                                <p>{#db_tick_messages#}:</p>
                                                                <div>
                                                                    <div class="ticket-message" ng-repeat="message in vars.currentTicket.messages">
                                                                        <div class="clearfix" ng-if="message.response == 1"><i class="icon icon-life-buoy" style="color: #f66b4f; float: left;"></i><p style="float: right;">[[message.date * 1000 | date:'yyyy-MM-dd HH:mm']]</p></div>
                                                                        <div class="clearfix" ng-if="message.response == 0"><i class="icon icon-life-buoy" style="color: #4591d7; float: left;"></i><p style="float: right;">[[message.date * 1000 | date:'yyyy-MM-dd HH:mm']]</p></div>
                                                                        <div><p>[[message.message]]</p></div>
                                                                        <hr>
                                                                        <div class="ticket-stripes" ng-class="message.response == 0 ? 'blue' : 'red'"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 ticket-none" ng-if="(vars.tickets.pending).length === 0">
                                                        <h6>{#db_tick_no_pending_tickets#}</h6>
                                                    </div>
                                                </div>

                                                <div class="row" ng-if="vars.closed">
                                                    <div class="clearfix" ng-if="(vars.tickets.closed).length !== 0">
                                                        <div class="col-md-2">
                                                            <h6>{#db_tick_ticket_no#}</h6>
                                                            <button class="btn btn-default btn-block" ng-repeat="ticket in vars.tickets.closed" ng-click="switchCurrentTicket(ticket)" ng-class="{ldelim}active:vars.currentTicket.ticket_id == ticket.ticket_id{rdelim}">[[ticket.ticket_id]]</button>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <div class="btn btn-default ticket-ctrl" ng-click="deleteTicket()">{#db_tick_delete_ticket#}</div>
                                                            <div class="btn btn-default ticket-ctrl" ng-click="reopenTicket()">{#db_tick_reopen_ticket#}</div>
                                                            <div style="width: 100%; margin: 15px 0;" ng-if="(vars.currentTicket).length !== 0">
                                                                <div class="clearfix" ng-if="vars.currentTicket.server_id != 0">
                                                                    <h6><span>{#db_tick_concerned_server#}:</span> <span>[[vars.currentTicket.server_name]]</span></h6>
                                                                </div>
                                                                <p>{#db_tick_messages#}:</p>
                                                                <div>
                                                                    <div class="ticket-message" ng-repeat="message in vars.currentTicket.messages">
                                                                        <div class="clearfix" ng-if="message.response == 1"><i class="icon icon-life-buoy" style="color: #f66b4f; float: left;"></i><p style="float: right;">[[message.date * 1000 | date:'yyyy-MM-dd HH:mm']]</p></div>
                                                                        <div class="clearfix" ng-if="message.response == 0"><i class="icon icon-life-buoy" style="color: #4591d7; float: left;"></i><p style="float: right;">[[message.date * 1000 | date:'yyyy-MM-dd HH:mm']]</p></div>
                                                                        <div><p>[[message.message]]</p></div>
                                                                        <hr>
                                                                        <div class="ticket-stripes" ng-class="message.response == 0 ? 'blue' : 'red'"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 ticket-none" ng-if="(vars.tickets.closed).length === 0">
                                                        <h6>{#db_tick_no_closed_tickets#}</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row ticket-message ticket-request-container">
                                                <h5>{#db_tick_request_ticket#}:</h5>
                                                <div>
                                                    <table class="ticket-request">
                                                        <tr>
                                                            <td>{#db_tick_issue#}:</td>
                                                            <td align="right">
                                                                <select
                                                                    ng-change="changeIssue()"
                                                                    ng-model="vars.type"
                                                                    >
                                                                    <option value="other">{#db_tick_sel00#}</option>
                                                                    <option value="network">{#db_tick_sel01#}</option>
                                                                    <option value="ipmanagement">{#db_tick_sel02#}</option>
                                                                    <option value="config">{#db_tick_sel03#}</option>
                                                                    <option value="hardware">{#db_tick_sel04#}</option>
                                                                    <option value="software">{#db_tick_sel05#}</option>
                                                                    <option value="invoicing">{#db_tick_sel06#}</option>
                                                                    <option value="balance">{#db_tick_sel07#}</option>
                                                                    <option value="details">{#db_tick_sel08#}</option>
                                                                    <option value="pendorder">{#db_tick_sel09#}</option>
                                                                    <option value="abuse">{#db_tick_sel10#}</option>
                                                                    <option value="domain">{#db_tick_sel11#}</option>
                                                                    <option value="cpanel">{#db_tick_sel12#}</option>
                                                                    <option value="additional">{#db_tick_sel13#}</option>
                                                                </select>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>{#db_tick_concerned_server#}:</td>
                                                            <td align="right">
                                                                <select
                                                                    ng-options="server as server.name for server in vars.servers"
                                                                    ng-model="vars.server"
                                                                    ng-change="changeServer()"
                                                                    >
                                                                </select>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>{#db_tick_question#}:</td>
                                                            <td align="right">
                                                                <textarea style="width: 100%;" ng-model="vars.message"></textarea>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>{#db_tick_append_file#}:</td>
                                                            <td align="right">
                                                                <div class="clearfix">
                                                                    <input type="file" class="form-control" ng-model="vars.file" style="width: 75%; float: left; margin-bottom: 10px;"><button class="btn btn-default" style="width: 22%; float: right;" ng-click="">{#db_tick_upload_file#}</button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <div class="btn-group btn-group-justified btn-group-lg customer-data-inf">
                                                        <div class="btn btn-default" ng-click="submitTicket()">{#db_tick_submit_ticket#}</div>
                                                    </div>
                                                </div>
                                                <div class="ticket-stripes blue"></div>
                                            </div>
                                        </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
