{include file='common/language/loadLanguage.tpl'}
<!DOCTYPE html>
<html lang="{$smarty.session.user.lang}" ng-app="bitShop">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

        <base href="{$base_href}/">

        {if !empty($hreflang)}
            {foreach from=$hreflang item=hl}
                <link rel="alternate" hreflang="{$hl.lang}" href="{$hl.href}">
            {/foreach}
        {/if}

        {if !empty($canonical_url)}
            <link rel="canonical" href="{$canonical_url}">
        {/if}

        <title>
            {* info from pages seo has highest priority *}
            {if !empty($seo)}
                {if ($seo.title eq 'Blog' || $seo.title eq 'FAQ')}
                    Host {$seo.title}
                {else}
                    {$seo.title|capitalize} | host
                {/if}
            {else if !empty($article.title)}
                {$article.title|capitalize} | host
            {else if !empty($page_title)}
                {$page_title|capitalize} | host
            {else}
        	host
            {/if}
        </title>

	<link href="/css/styles.css" rel="stylesheet">
	<link href="/css/jackedup.css" rel="stylesheet">
	<link href="/css/modalEffects.css" rel="stylesheet">
        <link href="/css/angular.rangeSlider.css" rel="stylesheet">

	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>

<!-- HEADER -->
<div id="header" class="header">
	<div class="top-bar">
		<div class="container">
			<div class="row">
				<div class="col-xs-6 top-bar-left">
					<div class="top-bar-item"><a href="{Helper::getFullURL("faq")}">{#faq_button#}</a> <i class="icon icon-spread"></i></div>
					<div class="top-bar-item">1 - 000 - 333 - 2266 <i class="icon icon-smartphone"></i></div>
				</div>
				<div class="col-xs-6 top-bar-right">
                                        <div class="top-bar-item language">
                                            <a href="/lang/en/" {if $smarty.session.user.lang eq 'en'}class="selected-language"{/if}>EN</a>
                                            <a href="/lang/ru/" {if $smarty.session.user.lang eq 'ru'}class="selected-language"{/if}>RU</a>
                                        </div>
					<div class="top-bar-item"><a href="#">{#live_chat#}</a> <i class="icon icon-dialogue-text"></i></div>
					{if $cart_count.count > 0}<div class="top-bar-item" id="top-items-count"><a href="{Helper::getFullURL("cart")}">{$cart_count.count} {$cart_count.products_declension}</a> <i class="icon icon-shopping-cart-content"></i></div>{/if}
					<div class="top-bar-item">{if !$user_logged}<a href="{Helper::getFullURL("login")}">{#login#}</a>{elseif $user_logged}<a href="{Helper::getFullURL("logout")}">{#logout#}</a>{/if} <i class="icon icon-profile"></i></div>
				</div>
			</div>

		</div>
	</div>
	<nav class="navbar navbar-default" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#collapsable-nav">
					<span class="sr-only">{#toggle_navigation#}</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{Helper::getFullURL("/")}"><span class="logo"></span></a>
			</div>

			<div id="collapsable-nav" class="collapse navbar-collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="{Helper::getFullURL("/")}">{#home#}</a></li>
					<li><a href="{Helper::getFullURL("about")}">{#about#}</a></li>
					{*<li><a href="{Helper::getFullURL("services")}">Services</a></li>*}
					<li><a href="{Helper::getFullURL("technology")}">{#technology#}</a></li>
					<li><a href="{Helper::getFullURL("products")}">{#products#}</a></li>
					<li><a href="{Helper::getFullURL("blog")}">{#blog#}</a></li>
                                        {if !$user_logged}
                                            {if $cart_count.count > 0}
                                                <li class="header-purchase">
                                                    <a class="btn btn-type2 btn-with-icon" href="{Helper::getFullURL("order")}">
                                                        <span class="btn-icon">
                                                            <span>
                                                                <span id="btn-items-count">{$cart_count.count}</span>
                                                                <i class="icon icon-shopping-cart-content"></i>
                                                            </span>
                                                        </span>
                                                        {#complete_order#}
                                                    </a>
                                                </li>
                                            {else}
                                                <li class="header-purchase">
                                                    <a class="btn btn-type2 btn-with-icon" href="{Helper::getFullURL("products")}">
                                                        <span class="btn-icon">
                                                            <span>
                                                                <i class="icon icon-shopping-cart-content"></i>
                                                            </span>
                                                        </span>
                                                        {#purchase#}
                                                    </a>
                                                </li>
                                            {/if}
                                        {else}
					<li class="dropdown header-dashboard">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{#dashboard#}<i class="spi spi-arrow-down"></i></a>
						<ul class="dropdown-menu" role="menu">
							<li {if $controller == 'dashboard' && $actionName == 'overview'}class="active"{/if}><a href="{Helper::getFullURL("dashboard/overview")}"><span><i class="icon icon-accelerator"></i></span>{#overview#}</a></li>
							<li {if $controller == 'dashboard' && $actionName == 'servers'}class="active"{/if}><a href="{Helper::getFullURL("dashboard/servers")}"><span><i class="icon icon-tv-monitor"></i></span>{#servers#}</a></li>
							{*<li {if $controller == 'dashboard' && $actionName == 'webspace'}class="active"{/if}><a href="{Helper::getFullURL("dashboard/webspace")}"><span><i class="icon icon-cloud-upload"></i></span>{#webspace#}</a></li>*}
							<li {if $controller == 'dashboard' && $actionName == 'billing'}class="active"{/if}><a href="{Helper::getFullURL("dashboard/billing")}"><span><i class="icon icon-shopping-cart-content"></i></span>{#billing#}</a></li>
							<li {if $controller == 'dashboard' && $actionName == 'settings'}class="active"{/if}><a href="{Helper::getFullURL("dashboard/settings")}"><span><i class="icon icon-setting-2"></i></span>{#settings#}</a></li>
							<li {if $controller == 'dashboard' && $actionName == 'security'}class="active"{/if}><a href="{Helper::getFullURL("dashboard/security")}"><span><i class="icon icon-lock-closed"></i></span>{#security#}</a></li>
							<li {if $controller == 'dashboard' && $actionName == 'support'}class="active"{/if}><a href="{Helper::getFullURL("dashboard/support")}"><span><i class="icon icon-life-buoy"></i></span>{#support#}</a></li>
							<li><a href="{Helper::getFullURL("logout")}"><span><i class="icon icon-key"></i></span>{#logout#}</a></li>
						</ul>
					</li>
                                        {/if}
				</ul>
			</div>
		</div>
	</nav>
        {*
        <div id="faq-button">
            <a href="{Helper::getFullURL("faq")}">{#faq_button#}</a>
        </div>
        *}
</div>
<!-- // HEADER -->

{include file=$action}

<!-- FOOTER BIG -->
<div class="footer-big" ng-controller="FooterController as footer" data-subscribed="{$news_subscribe_email}">
	<div class="container">
		<div class="row">
			<div class="col-md-3 footer-big-col">
				<h5>{#who_we_are#}</h5>
				<p><span class="logo logo-inverse"></span></p>
				<p>{#who_we_are_text#}</p>
				<div class="footer-big-more"><a href="{Helper::getFullURL("about")}">{#more_about_us#}</a> <i class="spi spi-arrow-right"></i></div>
			</div>
			<div class="col-md-3 footer-big-col">
				<h5>{#latest_tweets#}</h5>
				<ul class="footer-big-tweets">
                                    {foreach from=$latest_tweets item=tweet}
					<li>
						<div class="footer-big-tweet-text">{$tweet.text}</div>
						<div class="footer-big-tweet-tag">{$tweet.tag}</div>
						<div class="footer-big-tweet-time">{$tweet.time}</div>
					</li>
                                    {/foreach}
				</ul>
			</div>
			<div class="col-md-3 footer-big-col">
				<h5>{#get_in_touch#}</h5>
				<p>{#get_in_touch_text#}</p>
				<ul class="footer-big-contacts">
					<li><span><i class="icon icon-geolocalizator"></i></span>{#footer_address#}</li>
					<li><span><i class="icon icon-smartphone"></i></span>{#footer_phone#}</li>
					<li><span><i class="icon icon-mail"></i></span>{#footer_email#}</li>
				</ul>
			</div>
			<div class="col-md-3 footer-big-col">
				<h5>{#free_updates#}</h5>
				<p>{#free_updates_text#}</p>
                                <form name="subscribeNews">
				<div class="footer-big-subscribe ng-cloak">
					<div class="input-group">
						<input
                                                    type="email"
                                                    placeholder="{#free_updates_plc#}"
                                                    class="form-control"
                                                    ng-model="email"
                                                    ng-disabled="subscribed"
                                                    name="email"
                                                    >
						<span class="input-group-btn">
                                                    <button
                                                        ng-disabled="subscribed || !subscribeNews.email.$valid"
                                                        class="btn"
                                                        type="button"
                                                        ng-click="submit_subscribe()"
                                                        >
                                                        [[subscribed?'{#free_updates_subsd#}':'{#free_updates_subs#}']]
                                                    </button>
                                                </span>
					</div>
				</div>
                                </form>
			</div>
		</div>
	</div>
</div>
<!-- // FOOTER BIG -->

<!-- FOOTER SMALL -->
<div class="footer-small">
	<div class="footer-small-scroll-top">
		<a href="#header" class="scroll-to"><i class="glyphicon glyphicon-chevron-up"></i></a>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-sm-5 footer-small-left">© {'Y'|date} host. All rights reserved.</div>
			<div class="col-sm-7 footer-small-right">
				<ul class="nav nav-pills footer-small-nav">
					<li><a href="{Helper::getFullURL("/")}">{#home#}</a></li>
					<li><a href="{Helper::getFullURL("about")}">{#about#}</a></li>
					<li><a href="{Helper::getFullURL("products")}">{#products#}</a></li>
					{*<li><a href="{Helper::getFullURL("services")}">{#services#}</a></li>*}
					<li><a href="{Helper::getFullURL("blog")}">{#blog#}</a></li>
					<li><a href="{Helper::getFullURL("contact")}">{#contact#}</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- // FOOTER SMALL -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="/js/main.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular.min.js"></script>
<script src="/js/angular.rangeSlider.js"></script>
<script src="/js/humane.min.js"></script>
<script src="/js/classie.js"></script>
<script src="/js/modalEffects.js"></script>
<script src="https://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.12.1.js"></script>
<script src="/js/app.js"></script>

</body>
</html>