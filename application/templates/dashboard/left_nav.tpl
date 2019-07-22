<div class="col-md-3">
    <div class="dashboard-nav">
        <ul>
            <li><a href="{Helper::getFullURL("dashboard/overview")}"><i class="icon icon-home"></i>{#overview#}{if $actionName eq 'overview'} <i class="icon icon-arrow-right"></i>{/if}</a></li>
            {*<li><a href="{Helper::getFullURL("dashboard/services")}"><i class="icon icon-setting-2"></i>Services{if $actionName eq 'services'} <i class="icon icon-arrow-right"></i>{/if}</a></li>*}
            <li><a href="{Helper::getFullURL("dashboard/servers")}"><i class="icon icon-tv-monitor"></i>{#servers#}{if $actionName eq 'servers'} <i class="icon icon-arrow-right"></i>{/if}</a></li>
            {*<li><a href="{Helper::getFullURL("dashboard/webspace")}"><i class="icon icon-cloud-upload"></i>{#webspace#}{if $actionName eq 'webspace'} <i class="icon icon-arrow-right"></i>{/if}</a></li>*}
            <li><a href="{Helper::getFullURL("dashboard/billing")}"><i class="icon icon-shopping-cart-content"></i>{#billing#}{if $actionName eq 'billing'} <i class="icon icon-arrow-right"></i>{/if}</a></li>
            <li><a href="{Helper::getFullURL("dashboard/settings")}"><i class="icon icon-setting-2"></i>{#settings#}{if $actionName eq 'settings'} <i class="icon icon-arrow-right"></i>{/if}</a></li>
            <li><a href="{Helper::getFullURL("dashboard/security")}"><i class="icon icon-lock-closed"></i>{#security#}{if $actionName eq 'security'} <i class="icon icon-arrow-right"></i>{/if}</a></li>
            <li><a href="{Helper::getFullURL("dashboard/support")}"><i class="icon icon-life-buoy"></i>{#support#}{if $actionName eq 'support'} <i class="icon icon-arrow-right"></i>{/if}</a></li>
        </ul>
    </div>
</div>