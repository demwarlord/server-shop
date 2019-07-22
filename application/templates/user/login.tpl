<!-- LOGIN -->
<div class="normal-block" ng-controller="LoginController as login">
	<div class="container">
		<div class="login-box">
			<div class="login-box-header">{#login_authorize#}</div>
			<div class="login-box-content">
                            <form name="loginForm" action="{Helper::getFullURL("login")}" method="POST">
                                <div class="input-with-icon">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon icon-profile"></i></span>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="{#login_username#}"
                                                ng-model="vars.login"
                                                name="login"
                                            >
                                        </div>
                                </div>
                                <div class="input-with-icon">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon icon-lock-closed"></i></span>
                                            <input
                                                type="password"
                                                class="form-control"
                                                placeholder="{#login_password#}"
                                                ng-model="vars.password"
                                                name="password"
                                            >
                                        </div>
                                </div>
                                {if !empty($wrong_login)}
                                    <div class="wrong-login">
                                        {#wrong_login#}
                                    </div>
                                {/if}

                                <div class="login-forgot-pass"><a href="/forgot-password/">{#login_forgot_password#}</a></div>
                                <div class="login-submit">
                                        <button
                                            type="submit"
                                            class="btn btn-type2 btn-type3"
                                            >
                                            {#login_sign_in#}
                                        </button>
                                </div>
                            </form>
			</div>
		</div>
	</div>
</div>
<!-- // LOGIN -->