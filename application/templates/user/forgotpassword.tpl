<!-- LOGIN -->
<div class="normal-block" ng-controller="LoginController as login">
	<div class="container">
            <form name="forgotPassword">

		<div class="login-box">
			<div class="login-box-header">{#login_forgot_password#}</div>
			<div class="login-box-content">
				<div class="login-text">
					{#login_forgot_txt#}
				</div>
				<div class="input-with-icon">
					<div class="input-group">
						<span class="input-group-addon"><i class="icon icon-mail-open"></i></span>
						<input
                                                    ng-model="vars.email"
                                                    ng-disabled="vars.email_sent"
                                                    name="email"
                                                    type="email"
                                                    class="form-control"
                                                    placeholder="{#login_email#}"
                                                    >
					</div>
				</div>
				<div class="login-submit">
                                    <button
                                        ng-disabled="vars.email_sent || !forgotPassword.email.$valid"
                                        type="submit"
                                        class="btn btn-type2 btn-type3"
                                        ng-click="forgot_password()"
                                        >
                                        [[vars.email_sent?'{#login_email_sent#}':'{#login_email_submit#}']]
                                    </button>
                                </div>
			</div>
		</div>

            </form>
	</div>
</div>
<!-- // LOGIN -->
