<div class="container" id="cart" ng-controller="CartController as cart">
    <h2>{#cart_cart#}</h2>
    <div class="table-box">
        <table class="table">
            {foreach from=$cart_items name=icart key=key item=cart_item}
                <tr class="cart-item">
                    <td>
                        <h5 class="product-name">{$cart_item.caption}</h5>
                        {if $cart_item.products|@count > 0}
                            <table class="table table-condensed">
                                {foreach from=$cart_item.products item=product}
                                    <tr>
                                        <td>{$product.subcategory_name}</td>
                                        <td>{$product.short_name}</td>
                                        <td>
                                        {if isset($product.upgraded)}
                                            <span class="additional-price">+&nbsp;$&nbsp;{($product.price+$product.setup_fee)|string_format:'%.2f'}</span>
                                        {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                                <tr>
                                    <td>{#cnf_server_location#}</td>
                                    <td colspan="2">{$cart_item.server_location.location_name}</td>
                                </tr>
                                <tr class="ng-cloak" ng-if="vars.comments[{$cart_item.cart_item_id}] != '' || vars.comments_toggled.indexOf({$cart_item.cart_item_id}) !== -1">
                                    <td>{#cnf_server_comment#}</td>
                                    <td colspan="2">
                                        <textarea
                                            style="min-width: 95%; min-height: 60px; resize: vertical; padding: 10px;"
                                            ng-model="vars.comments[{$cart_item.cart_item_id}]"
                                            ng-init="vars.comments[{$cart_item.cart_item_id}] = '{$cart_item.comment}'"
                                            ng-disabled="vars.comments_toggled.indexOf({$cart_item.cart_item_id}) == -1"
                                            >
                                        </textarea>
                                        <br/>
                                        <button
                                            ng-if="vars.comments_toggled.indexOf({$cart_item.cart_item_id}) !== -1"
                                            class="btn btn-primary"
                                            type="button"
                                            ng-click="save_comment({$cart_item.cart_item_id})"
                                            >
                                            {#cnf_btn_save_comment#}
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        {/if}
                    </td>

                    <td>
                        {if $cart_item.discount > 0}
                            <div class="price monthly-price-discount">
                                <span>{#cart_monthly#}</span>
                                <span><b>$&nbsp;{($cart_item.complete_monthly_fee*$cart_item.quantity)|string_format:'%.2f'}</b></span>
                                <span>$&nbsp;{(($cart_item.monthly_fee+$cart_item.products_monthly_fee)*$cart_item.quantity)|string_format:'%.2f'}</span>
                            </div>
                        {else}
                            <div class="price monthly-price">
                                <span>{#cart_monthly#}</span>
                                <span><b>$&nbsp;{($cart_item.complete_monthly_fee*$cart_item.quantity)|string_format:'%.2f'}</b></span>
                            </div>
                        {/if}
                        <div class="price setup-fee">
                            <span>{#cart_setup_fee#}</span>
                            <span><b>$&nbsp;{(($cart_item.complete_setup_fee)*$cart_item.quantity)|string_format:'%.2f'}</b></span>
                        </div>
                    </td>
                    <td>
                        <div class="count">
                            <i class="glyphicon glyphicon-minus-sign" ng-click="change_quantity({$cart_item.cart_item_id}, {$cart_item.quantity}-1);"></i>
                            <input type="text" disabled="" data-cart-item-id="{$cart_item.cart_item_id}" value="{$cart_item.quantity}"/>
                            <i class="glyphicon glyphicon-plus-sign" ng-click="change_quantity({$cart_item.cart_item_id}, {$cart_item.quantity}+1);"></i>
                        </div>
                    </td>
                    <td>
                        <a href="#" onclick="return false;" ng-click="toggle_comment({$cart_item.cart_item_id})" class="comment-item"><i class="glyphicon glyphicon-comment"></i></a>
                        <a href="{Helper::getFullURL("cart")}" ng-click="remove_from_cart({$cart_item.cart_item_id})" class="remove-item"><i class="glyphicon glyphicon-trash"></i></a>
                        <a href="{Helper::getFullURL("/edit/{$key}")}" data-cart-item-id="{$cart_item.cart_item_id}" class="edit-item"><i class="glyphicon glyphicon-edit"></i></a>
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
    <div class="table-box clearfix cart-sum">
        <table class="prices-wrapper pull-right">
        <tr>
            {if $discount > 0}
            <td class="const-discount">
                <span>
                    {#cart_discount#}:
                </span>
                <span class="total-sum total-discount" data-total-discount="{$discount|string_format:'%.2f'}">
                    -&nbsp;$&nbsp;{$discount|string_format:'%.2f'}
                </span>
            </td>
            {/if}
            <td class="const-setup">
                <span>
                    {#cart_setup_fee#}:
                </span>
                <span class="total-sum total-setup-price" data-server-setup-price="{$setup|string_format:'%.2f'}" data-total-setup-price="{$setup|string_format:'%.2f'}">
                    $&nbsp;{$setup|string_format:'%.2f'}
                </span>
            </td>
            <td class="const-price">
                <span>
                    {#cart_monthly#}:
                </span>
                <span class="total-sum total-monthly-price" data-server-monthly-price="{$complete_monthly|string_format:'%.2f'}" data-total-monthly-price="{$complete_monthly|string_format:'%.2f'}">
                    $&nbsp;{$complete_monthly|string_format:'%.2f'}
                </span>
            </td>
            <td class="const-total">
                <span>
                    &#931;
                </span>
                <span class="total-sum total-final-price" data-total-final-price="{$cart_total|string_format:'%.2f'}">
                    $&nbsp;{$cart_total|string_format:'%.2f'}
                </span>
            </td>
            <td class="const-button">
                <a href="{Helper::getFullURL("order")}"><button class="btn btn-lg btn-primary">
                    {#cart_checkout#}
                </button></a>
            </td>
        </tr>
        </table>
    </div>
</div>