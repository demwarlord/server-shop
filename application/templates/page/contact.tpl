<!-- CONTACT US -->
<div class="normal-block ng-cloak" ng-controller="ContactController as contact">
	<div class="container">

		<div class="row">
			<div class="col-md-8">
				<div class="content-block contact-us">
                                    <div class="ask-thank-you" ng-if="asked">
                                        {#ask_thank_you#}
                                        <pre ng-bind="resp"></pre>
                                    </div>
                                    <form name="contactForm" ng-if="!asked">
                                        <h3 class="content-block-heading">{#ask_a_question#}</h3>
					<div class="form-inf">{#ask_a_question_txt#}</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text">{#ask_department#} *:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <select
                                                        name="ask_department"
                                                        ng-model="ask.department"
                                                        required
                                                        >
                                                        <option value="0">{#ask_sel_dep0#}</option>
                                                        <option value="1">{#ask_sel_dep1#}</option>
                                                        <option value="2">{#ask_sel_dep2#}</option>
                                                    </select>
                                                </div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text">{#ask_data_center_location#} *:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <select
                                                        name="ask_location"
                                                        ng-model="ask.location"
                                                        required
                                                        >
                                                        <option value="0">{#ask_sel_dc0#}</option>
                                                        <option value="1">{#ask_sel_dc1#}</option>
                                                        <option value="2">{#ask_sel_dc2#}</option>
                                                    </select>
                                                </div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text">{#ask_first_name#} *:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <input
                                                        name="ask_first_name"
                                                        ng-model="ask.first_name"
                                                        ng-minlength="2"
                                                        ng-maxlength="100"
                                                        required
                                                        type="text"
                                                        class="form-control"
                                                        />
                                                </div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text">{#ask_last_name#} *:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <input
                                                        name="ask_last_name"
                                                        ng-model="ask.last_name"
                                                        ng-minlength="2"
                                                        ng-maxlength="100"
                                                        required
                                                        type="text"
                                                        class="form-control"
                                                        />
                                                </div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text">{#ask_email_address#} *:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <input
                                                        name="ask_email_address"
                                                        ng-model="ask.email_address"
                                                        ng-minlength="2"
                                                        ng-maxlength="100"
                                                        required
                                                        type="email"
                                                        class="form-control"
                                                        />
                                                </div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text">{#ask_phone#} *:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <input
                                                        name="ask_phone"
                                                        ng-model="ask.phone"
                                                        ng-minlength="5"
                                                        ng-maxlength="32"
                                                        ng-pattern="/^[0-9]+$/"
                                                        required
                                                        type="text"
                                                        class="form-control"
                                                        />
                                                </div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text">{#ask_question#} *:</div>
						<div class="col-sm-8 col-md-6 server-config-choice">
                                                    <textarea
                                                        name="ask_question"
                                                        ng-model="ask.question"
                                                        ng-minlength="16"
                                                        ng-maxlength="2048"
                                                        required
                                                        cols="30"
                                                        rows="6"
                                                        class="form-control"
                                                        >
                                                    </textarea>
                                                </div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text"></div>
						<div class="col-sm-8 col-md-6 server-config-choice contact-call-back">
                                                    <label>
                                                        <input
                                                            name="ask_callback"
                                                            ng-model="ask.callback"
                                                            type="checkbox"
                                                            />
                                                        {#ask_callback#}
                                                    </label>
                                                </div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text"></div>
						<div class="col-sm-8 col-md-6 server-config-choice contact-captcha">
							<p>{#ask_check_img#}</p>
							<div class="captcha-image">
                                                            <img
                                                                src="/get-captcha/"
                                                                alt=""
                                                                />
                                                        </div>
							<div>
                                                            <input
                                                                name="ask_check_img"
                                                                ng-model="ask.check_img"
                                                                ng-minlength="6"
                                                                ng-maxlength="6"
                                                                required
                                                                class="form-control"
                                                                type="text"
                                                                />
                                                        </div>
						</div>
					</div>
					<div class="row server-config-line">
						<div class="col-sm-4 col-md-6 server-config-text"></div>
						<div class="col-sm-8 col-md-6 server-config-choice contact-submit">
                                                    <button
                                                        type="submit"
                                                        class="btn btn-type2"
                                                        ng-click="submit_question()"
                                                        ng-disabled="contactForm.$invalid"
                                                        >
                                                        {#ask_submit#}
                                                    </button>
                                                </div>
					</div>
                                    </form>
				</div>
			</div>
			<div class="col-md-4">
				<div class="content-block">
					<h3 class="content-block-heading">{#ci_contact_info#}</h3>

					<div class="contact-info-block">
						<header>{#ci_business_hours#}:</header>
						<div>{#ci_business_hours_txt#}</div>
					</div>
					<div class="contact-info-block">
						<header>{#ci_telephone_fax#}: </header>
						<div>{#ci_telephone_fax_txt#}</div>
					</div>
					<div class="contact-info-block">
						<header>{#ci_visitors_address#}: </header>
						<div>{#ci_visitors_address_txt#}</div>
					</div>
					<div class="contact-info-block">
						<header>{#ci_sales#}:</header>
						<div>{#ci_sales_txt#}</div>
					</div>
					<div class="contact-info-block">
						<header>{#ci_support#}:</header>
						<div>{#ci_support_txt#}</div>
					</div>
					<div class="contact-info-block">
						<header>{#ci_billing#}:</header>
						<div>{#ci_billing_txt#}</div>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
<!-- // CONTACT US -->
