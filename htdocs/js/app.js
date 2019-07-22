(function () {
    var app = angular.module('bitShop', ['ui.bootstrap', 'ui-rangeSlider']);
    var jacked_success = humane.create({baseCls: 'humane-jackedup', addnCls: 'humane-jackedup-success'});
    var jacked_error = humane.create({baseCls: 'humane-jackedup', addnCls: 'humane-jackedup-error'});

    var lng = document.documentElement.lang;
    var txt = {
            ru : {
                mo : 'мес',
                month : 'месяц',
                setup : 'Установка',
                save : 'Экономия',
                select_country : 'Выберите страну',
                select_prefix : 'Код',
                login_wrong: 'Неправильный логин или пароль',
                error : 'Произошла ошибка',
                db_saved : 'Сохранено',
                db_no_data : 'НЕТ ДАННЫХ',
                db_request_accd : 'Запрос на изменения был принят',
                db_btn_pay : 'ОПЛАТИТЬ',
                db_btn_fltr : 'ПРИМЕНИТЬ ФИЛЬТР',
                db_btn_pwd : 'Изменить пароль',
                db_btn_subm : 'Запросить изменения',
                db_btn_rsent : 'Запрос отправлен успешно',
                db_btn_erch : 'Ошибка изменения информации',
                db_btn_pchd : 'Пароль изменен успешно',
                db_btn_epch : 'Ошибка изменения пароля или уже был использован',
                db_tckt_noissue : 'Безотносительно сервера',
                db_tckt_short : 'Сообщение слишком короткое!',
                ask_choose : 'Пожалуйста, выберите департамент и расположение датацентра',
                ask_errorcode : 'Вы ввели неправильный проверочный код',
                select_location : 'Выберите датацентр',
                server_location_error : 'Выберите расположение сервера',
                fltr_cpu : 'Процессор',
                fltr_cpu_cores : 'Ядер',
                fltr_cpu_number : 'Процессоров',
                fltr_ram : 'Память',
                fltr_hdd_type : 'Тип хранилища',
                fltr_hdd : 'Объем хранилища',
                fltr_hdd_number : 'Кол-во дисков',
                fltr_band : 'Сеть',
                fltr_location : 'Датацентр',
                fltr_price : 'Цена'
            },
            en : {
                mo : 'mo',
                month : 'month',
                setup : 'Setup',
                save : 'Save',
                select_country : 'Select Country',
                select_prefix : 'Prefix',
                login_wrong: 'Wrong login or password',
                error : 'Error has occured',
                db_saved : 'Saved',
                db_no_data : 'NO DATA',
                db_request_accd : 'Your request was accepted',
                db_btn_pay : 'PAY NOW',
                db_btn_fltr : 'APPLY FILTER',
                db_btn_pwd : 'Change password',
                db_btn_subm : 'Submit changes',
                db_btn_rsent : 'Request was sent successfully',
                db_btn_erch : 'Error changing information',
                db_btn_pchd : 'Password changed successfully',
                db_btn_epch : 'Error changing password or already used',
                db_tckt_noissue : 'No server related issue',
                db_tckt_short : 'Message is too short!',
                ask_choose : 'Please choose department and location',
                ask_errorcode : 'You entered wrong check code',
                select_location : 'Select Datacentre Location',
                server_location_error : 'Please select datacentre location',
                fltr_cpu : 'CPU',
                fltr_cpu_cores : 'Cores',
                fltr_cpu_number : 'Processors',
                fltr_ram : 'RAM',
                fltr_hdd_type : 'Storage Type',
                fltr_hdd : 'Storage Capacity',
                fltr_hdd_number : 'Drive Amounts',
                fltr_band : 'Network',
                fltr_location : 'Location',
                fltr_price : 'Price'
            }
        };

    app.run(function($rootScope) {
        $rootScope.isEmpty = function(obj) {
            if (obj === undefined || obj === null) return true;
            if (obj === false || obj === true) return !obj;
            if (Array.isArray(obj)) {
                if (obj.length > 0) return false;
                else return true;
            }
            if (obj !== Object(obj)) { // Not an object (primitive)
                console.log('isEmpty: primitive');
                return true;
            }
            return (Object.getOwnPropertyNames(obj).length === 0);
        };
    });

    app.config(['$interpolateProvider', '$httpProvider', function ($interpolateProvider, $httpProvider) {
            $interpolateProvider.startSymbol('[[');
            $interpolateProvider.endSymbol(']]');
            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
        }]);

    app.value('periods', [
            {period: 1, discount: 0},
            {period: 3, discount: 2.5},
            {period: 6, discount: 5},
            {period: 12, discount: 10}
        ]);

    app.filter('toArray', function () {
        'use strict';

        return function (obj) {
            if (!(obj instanceof Object)) {
                return obj;
            }

            return Object.keys(obj).map(function (key) {
                return Object.defineProperty(obj[key], '$key', {__proto__: null, value: key});
            });
        };
    });

    app.filter('beautify', function () {
        return function (v, suffix) {
            if (suffix.substring(0,1) == '+') {
                return v + suffix.slice(1);
            } else if (suffix.substring(0,1) == '-') {
                return suffix.slice(1) + v;
            } else {
                return v;
            }
        };
    });

    app.filter('filter_products', function () {
        'use strict';

        return function (items, cat_key, uniques, fields, vars) {
            var filtered = [];

            for (var i in items) {
                var item = items[i],
                    add_item = true;

                for (var field_idx in fields) {
                    var field = fields[field_idx].field,
                        field_uniques = uniques[field_idx],
                        field_type = fields[field_idx].type,
                        sub_field = fields[field_idx].sub_field,
                        field_mode = fields[field_idx].mode;

                    var filter_on_this = false;

                    for (var uniq_idx in field_uniques) {
                        if (field_uniques[uniq_idx].selected) {
                            filter_on_this = true;
                            break;
                        }
                    }

                    if (!filter_on_this) {
                        continue;
                    } else {
                        add_item = false;
                    }

                    for (var uniq_idx in field_uniques) {
                        var uniq = field_uniques[uniq_idx];

                        if (field_mode == 'array') {
                            var expr = 'item.' + field;
                            var rr = eval(expr);

                            for (var r in rr) {
                                var expr = 'rr[r].' + (sub_field == '' ? '' : sub_field);
                                var ev = eval(expr);

                                if (ev === undefined) { continue; }

                                var vv = ev.toString();

                                if (field_type === 'discrete' && uniq.selected && uniq.val === vv) {
                                    add_item = true;
                                    break;
                                }

                                if (field_type === 'range' && uniq.max >= parseFloat(vv) && uniq.min <= parseFloat(vv)) {
                                    add_item = true;
                                    break;
                                }
                            }

                            if (add_item) {
                                break;
                            }

                        } else if (field_mode == 'single') {
                            var expr = 'item.' + field + (sub_field == '' ? '' : '.' + sub_field);
                            var ev = eval(expr);

                            if (ev === undefined) { continue; }

                            var vv = ev.toString();

                            if (field_type === 'discrete' && uniq.selected && uniq.val === vv) {
                                add_item = true;
                                break;
                            }

                            if (field_type === 'range' && uniq.max >= parseFloat(vv) && uniq.min <= parseFloat(vv)) {
                                add_item = true;
                                break;
                            }
                        }

                    }

                    if (!add_item) {
                        break;
                    }
                }

                if (add_item) {
                    filtered.push(items[i]);
                }
            }

            var total_items = filtered.length, pages = Math.ceil(total_items/vars.items_per_page);
            vars.pagination[cat_key].pages = (pages > 0 ? pages - 1 : 0);

            if (vars.pagination[cat_key].page > vars.pagination[cat_key].pages) {
                vars.pagination[cat_key].page = vars.pagination[cat_key].pages;
            }

            //console.log(cat_key + " : " + vars.pagination[cat_key].page + " : OF:" + vars.pagination[cat_key].pages)
            return filtered.slice(vars.pagination[cat_key].page*vars.items_per_page,vars.pagination[cat_key].page*vars.items_per_page+vars.items_per_page);
        };
    });

    app.controller('ProductsController', function ($http, $scope, $attrs) {
        $scope.data = {};

        $scope.filter_fields = [
            //{field: 'cpu[0].short_name', sub_field: '', value_field: 'cpu[0].value', desc: txt[lng].fltr_cpu, type: 'discrete', mode: 'single'},
            {field: 'cpu[0].properties.number', sub_field: '', value_field: 'cpu[0].properties.number', desc: txt[lng].fltr_cpu_number, type: 'discrete', mode: 'single'},
            {field: 'cpu[0].properties.cores', sub_field: '', value_field: 'cpu[0].properties.cores', desc: txt[lng].fltr_cpu_cores, type: 'discrete', mode: 'single'},
            {field: 'ram[0].properties.value', sub_field: '', value_field: '', desc: txt[lng].fltr_ram, type: 'range', mode: 'single', suffix: '+ GB'},
            {field: 'hdd[0].properties.type', sub_field: '', value_field: 'hdd[0].properties.type', desc: txt[lng].fltr_hdd_type, type: 'discrete', mode: 'single'},
            {field: 'hdd[0].properties.number', sub_field: '', value_field: '', desc: txt[lng].fltr_hdd_number, type: 'range', mode: 'single', suffix: '!'},
            {field: 'hdd[0].properties.value', sub_field: '', value_field: '', desc: txt[lng].fltr_hdd, type: 'range', mode: 'single', suffix: '+ GB'},
            {field: 'band[0].short_name', sub_field: '', value_field: 'band[0].value', desc: txt[lng].fltr_band, type: 'discrete', mode: 'single'},
            {field: 'location_info', sub_field: 'location_name', value_field: '', desc: txt[lng].fltr_location, type: 'discrete', mode: 'array'},
            {field: 'complete_monthly_fee', sub_field: '', value_field: '', desc: txt[lng].fltr_price, type: 'range', mode: 'single', suffix: '-$ '}
        ];

        $scope.uniques = {};

        $scope.vars = {
            'items_per_page' : 30,
            'order_by' : 'caption',
            'order_reversed' : false,
            'loaded' : false
        };

        $scope.change_order = function (key) {
            if ($scope.vars.order_by == key) {
                $scope.vars.order_reversed = !$scope.vars.order_reversed;
                $scope.vars.order_by = ($scope.vars.order_reversed ? '-' : '') + key;
            } else {
                $scope.vars.order_reversed = false;
                $scope.vars.order_by = key;
            }
        };

        $scope.natural_order = function (a) {
            var dir = $scope.vars.order_by.indexOf('-'),
                key = '';

            if (dir == -1) {
                key = $scope.vars.order_by;
                dir = 1;
            } else {
                key = $scope.vars.order_by.substring(1);
                dir = -1;
            }

            var expr = 'a.' + key;
            var r = eval(expr).toString();

            if (!isNaN(parseInt(r.substring(0,1)))) {
                return (parseInt(r.replace(/^\D+/g,'')));
            } else {
                r = r.toLowerCase().replace(/\s+/g,'');
                return r;
            }
        };

        $http({method: 'POST', url: '/ajax/getServers/'})
            .success(function (response) {
                if (response != 0) {
                    $scope.data = response;

                    // Find uniques

                    for (var srv_type in $scope.data.servers) {
                        for (var srv_group in $scope.data.servers[srv_type]) {
                            for (var srv in $scope.data.servers[srv_type][srv_group]) {
                                var srv_data = $scope.data.servers[srv_type][srv_group][srv];
                                var cat = srv_data.category_id;

                                for (var cats in $scope.data.categories) {
                                    if (srv_data.category_id == $scope.data.categories[cats].id) {
                                        var key = $scope.data.categories[cats].slug;
                                    }
                                }

                                if (key !== undefined) {
                                    for (var field_idx in $scope.filter_fields) {
                                        var field = $scope.filter_fields[field_idx].field,
                                            sub_field = $scope.filter_fields[field_idx].sub_field,
                                            value_field = $scope.filter_fields[field_idx].value_field,
                                            type = $scope.filter_fields[field_idx].type,
                                            mode = $scope.filter_fields[field_idx].mode;

                                        if (mode == 'single') {
                                            var expr = 'srv_data.' + field + (sub_field == '' ? '' : '.' + sub_field);
                                            var r = eval(expr);
                                            if (r === undefined) { continue; }
                                            var rr = [r];

                                            if (value_field != '') {
                                                var expr_evf = 'srv_data.' + value_field;
                                                var evf = eval(expr_evf);
                                                if (evf === undefined) { evf = 0; }
                                            } else {
                                                var evf = 0;
                                            }

                                        } else if (mode == 'array') {
                                            var expr = 'srv_data.' + field;
                                            var r = eval(expr);
                                            if (r === undefined) { continue; }
                                            var rr = r;
                                        }

                                        for (var r_idx in rr) {
                                            if (mode == 'array') {
                                                var expr = 'rr[r_idx].' + (sub_field == '' ? '' : sub_field);
                                                var r = eval(expr);
                                                if (r === undefined) { continue; }
                                                var vv = r.toString();

                                                if (value_field != '') {
                                                    var expr_evf = 'rr[r_idx].' + value_field;
                                                    var evf = eval(expr_evf);
                                                    if (evf === undefined) { evf = 0; }
                                                } else {
                                                    var evf = 0;
                                                }

                                            } else if (mode == 'single') {
                                                var vv = rr[r_idx].toString();
                                            }

                                            if ($scope.uniques[key] === undefined) {
                                                $scope.uniques[key] = [];
                                            }

                                            if ($scope.uniques[key][field_idx] === undefined) {
                                                $scope.uniques[key][field_idx] = [];
                                            }

                                            if (type == 'discrete' && $scope.uniques[key][field_idx].map(function(e) { return e.val; }).indexOf(vv) === -1) {
                                                if (evf !== undefined && $scope.uniques[key][field_idx].length > 0) {
                                                    for (var i = 0; i <= $scope.uniques[key][field_idx].length; i++) {
                                                         if (i === $scope.uniques[key][field_idx].length || evf <= $scope.uniques[key][field_idx][i].sort_val) {
                                                             $scope.uniques[key][field_idx].splice(i, 0, {val:vv, sort_val: evf, selected:false});
                                                             break;
                                                         }
                                                    }
                                                } else {
                                                    $scope.uniques[key][field_idx].push({val:vv, sort_val: (evf !== undefined ? 0 : evf), selected:false});
                                                }
                                            } else if (type == 'range') {
                                                vv = parseFloat(vv);

                                                if ($scope.uniques[key][field_idx][0] !== undefined) {
                                                    var uniq = $scope.uniques[key][field_idx][0];

                                                    if (vv > uniq.max) {
                                                        uniq.max = vv;
                                                    }

                                                    if (vv < uniq.min) {
                                                        uniq.min = vv;
                                                    }
                                                } else {
                                                    $scope.uniques[key][field_idx][0] = {max: vv, min: vv, selected: true, suffix: $scope.filter_fields[field_idx].suffix};
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $scope.vars.loaded = true;
                } else {
                    jacked_error.log(txt[lng].error);
                }
            })
            .error(function () {
                jacked_error.log(txt[lng].error);
            });
    });

    app.controller('ConfiguratorController', function ($http, $scope, $attrs, $sce, $window, priceManager, periods) {
        $scope.srv_products = [];
        $scope.selected_products = [];
        $scope.periods = periods;
        $scope.locations = [];
        $scope.srv = {};
        $scope.vars = {
            'min_period' : $scope.periods[0].period,
            'quantity' : 1,
            'location' : {},
            'comment_toggle' : false,
            'comment' : '',
            'loaded' : false
        };

        if ($attrs.configured == 1) {
            var param = {
                edit : $attrs.configured,
                cart_item_id: $attrs.cartItemId,
                server_url: $attrs.url
            };
        } else {
            var param = {
                edit : $attrs.configured,
                server_url: $attrs.url
            };
        }

        $http({method: 'POST', url: '/ajax/getServerData/', data: param})
            .success(function (response) {
                if (response != 0) {
                    $scope.srv = response.server_data;
                    $scope.locations = $scope.locations.concat(response.server_data.location_info);
                    $scope.vars.location = $scope.locations[0];

                    if ($attrs.configured == 1) {
                        //$scope.selected_products = angular.copy(response.selected_products);
                        $scope.selected_products = Object.keys(response.selected_products).map(function(k) { return response.selected_products[k] });
                        $scope.vars.min_period = response.min_period;
                        $scope.vars.quantity = response.quantity;

                        for (var i in $scope.locations) {
                           if ($scope.locations[i].id == response.location.id) {
                                $scope.vars.location = $scope.locations[i];
                           }
                        }

                        $scope.vars.comment = response.comment;

                        if ($scope.vars.comment != '') {
                            $scope.vars.comment_toggle = true;
                        }
                    }

                    for (var subcategory in response.server_data.sub_categories) {
                        this.a = angular.copy(response.server_data.sub_categories[subcategory].products[0]);
                        delete this.a.upgrade_products;
                        delete this.a.upgrade;

                        if ($attrs.configured == 0) {
                            $scope.selected_products.push(this.a);
                        }

                        this.b = angular.copy(response.server_data.sub_categories[subcategory]);
                        this.b.products = [];
                        this.b.products.push(this.a);

                        for (var upgrade in response.server_data.sub_categories[subcategory].products[0].upgrade_products) {
                            this.c = angular.copy(response.server_data.sub_categories[subcategory].products[0].upgrade_products[upgrade]);
                            this.b.products.push(this.c);
                        }

                        $scope.srv_products.push(this.b);
                    }

                    $scope.vars.loaded = true;
                }
        });

        $scope.replace_product = function (product) {
            for (var prod in $scope.selected_products) {
                if (product.sub_category_id == $scope.selected_products[prod].sub_category_id) {
                    $scope.selected_products[prod] = product;
                }
            }

            var min_period = 1;

            for (var prod in $scope.selected_products) {
                if ($scope.selected_products[prod].minimal_period > min_period) {
                    min_period = $scope.selected_products[prod].minimal_period;
                }
            }

            $scope.vars.min_period = parseInt(min_period);
        };

        $scope.formatting_prods = function (prod) {
            if (prod.is_default_product == 1) {
                return prod.name;
            } else {
                return (prod.name +
                        (prod.price > 0 ? ' (+ $' + prod.price + '/' + txt[lng].mo.toLowerCase() + (prod.setup_fee > 0 ? '' : ')') : '') +
                        (prod.setup_fee > 0 ? (prod.price > 0 ? ' ' : ' (') + '+ $' + prod.setup_fee + '/' + txt[lng].setup.toLowerCase() + ')' : ''));
            }
        };

        $scope.find_selected = function (products) {
            for (var prod in products) {
                for (var sel_prod in $scope.selected_products) {
                    if (products[prod].id == $scope.selected_products[sel_prod].id) {
                        return products[prod];
                    }
                }
            }
            return products[0];
        };

        $scope.add_to_cart = function () {
            if ($scope.vars.location.id == 0) {
                jacked_error.log(txt[lng].server_location_error);
                return;
            }
            if ($attrs.configured == 1) {
                var param = {
                    edit : $attrs.configured,
                    cart_item_id: $attrs.cartItemId,
                    order_data: $scope.selected_products,
                    quantity: $scope.vars.quantity,
                    location : $scope.vars.location.location_name_en,
                    comment : $scope.vars.comment,
                    server_url: $attrs.url
                };
            } else {
                var param = {
                    edit : $attrs.configured,
                    order_data: $scope.selected_products,
                    quantity: $scope.vars.quantity,
                    location : $scope.vars.location.location_name_en,
                    comment : $scope.vars.comment,
                    server_url: $attrs.url
                };
            }

            $http({method: 'POST', url: '/ajax/addCartItem/', data: param})
                .success(function (data) {
                    if (data == 1) {
                        $window.location.href = '/cart/';
                    }
                });
        };

        $scope.change_count = function (c) {
            if (($scope.vars.quantity + c) < 1) {
                $scope.vars.quantity = 1;
            } else if (($scope.vars.quantity + c) > 10) {
                $scope.vars.quantity = 10;
            } else {
                $scope.vars.quantity = $scope.vars.quantity + c;
            }
        };

        $scope.myHTMLinAdditional = function (data) {
            return $sce.trustAsHtml('<span class="text-success server-additional-price">+ $ ' + data.price + '/' + txt[lng].mo + '</span><span class="prod-label">' + data.label + '</span> : <span class="prod-caption">' + data.name + '</span>');
        };

        $scope.myHTMLinPeriods = function (period) {
            var sums = priceManager.calculateTotals($scope, period, $scope.vars.quantity);

            return $sce.trustAsHtml(
                        '<div class="payment-col payment-period-value">' + period.period + ' ' + txt[lng].month + ' x <strong>$ ' + sums.total_mo.toFixed(2) + '</strong><br/>(' + txt[lng].setup + ': $' + sums.total_setup.toFixed(2) + (sums.old_setup > 0 ? ' <span class="was-price"> $' + sums.old_setup.toFixed(2) + '</span>' : '') +')</div>' +
                        '<div class="payment-col payment-total">' +
                            '<div class="payment-total-value"><strong>$ ' + sums.total_sum.toFixed(2) + '</strong></div>' +
                            '<div class="payment-total-save">' + txt[lng].save + ': $ ' + sums.discount.toFixed(2) + '</div>' +
                        '</div>'
                    );
        };
    });

    app.controller('CartController', function ($http, $scope, $window) {
        $scope.vars = {
            'comments_toggled' : [],
            'comments' : {}
        };

        $scope.change_quantity = function (cart_item_id, number) {
            if (number > 0 && number < 11) {
                $http({method: 'POST', url: '/ajax/changeCartItemQuantity/', data: {cart_item_id: cart_item_id, number: number}})
                    .success(function (response) {
                        if (response != 0 && response.errorcode == 1) {
                            $window.location.href = '/cart/';
                        }
                    });
            }
        };

        $scope.remove_from_cart = function (cart_item_id) {
            $http({method: 'POST', url: '/ajax/removeCartItem/', data: {cart_item_id: cart_item_id}})
                .success(function (data) {
                    if (data == 1) {
                        $window.location.href = '/cart/';
                    }
                });
        };

        $scope.save_comment = function (cart_item_id) {
            $http({method: 'POST', url: '/ajax/saveComment/', data: {cart_item_id: cart_item_id, comment: $scope.vars.comments[cart_item_id]}})
                .success(function (data) {
                    if (data == 1) {
                        $scope.vars.comments_toggled.splice($scope.vars.comments_toggled.indexOf(cart_item_id), 1);
                    }
                });
        };

        $scope.toggle_comment = function (cart_item_id) {
            if ($scope.vars.comments_toggled.indexOf(cart_item_id) === -1) {
                $scope.vars.comments_toggled.push(cart_item_id);
            } else {
                $scope.vars.comments_toggled.splice($scope.vars.comments_toggled.indexOf(cart_item_id), 1);
            }
        };
    });

    app.controller('OrderController', function ($http, $scope, $attrs, $sce, priceManager, periods, $window, $timeout) {
        $scope.periods = periods;
        $scope.cart = {};
        $scope.vars = {
            'user_logged' : parseInt($attrs.userLogged),
            'user_id' : ($attrs.userLogged == 1 ? parseInt($attrs.userId) : 0),
            'user_vat_rate' : 0,
            'min_period' : $scope.periods[0].period,
            'payment_method' : 'visa',
            'selected_period' : $scope.periods[0].period,
            'countries' : [
                { "code": 0, "value": "0", "label": txt[lng].select_country }
            ],
            'prefixes' : [
                { "value": 0, "label": txt[lng].select_prefix }
            ],
            'stage_1_loaded' : false,
            'stage_2_loaded' : false,
            'stage_3_loaded' : false,
            'email_in_validation' : false,
            'email_validated' : false,
            'email_validation_code' : ''
        };
        $scope.reg = {
            status : 1, // 1-business 0-personal
            post_code : '',
            city : '',
            country : $scope.vars.countries[0],
            first_name : '',
            last_name : '',
            email_address : '',
            phone_prefix : $scope.vars.prefixes[0],
            phone : '',
            password : '',
            confirm_password : '',
            gender : '0',
            language : 'en',
            address : '',
            business : {
                company_name : '',
                position : '0',
                vat : '',
            }
        };

        $http.get("/js/countries.json")
            .success(function(response) {
                $scope.vars.countries = $scope.vars.countries.concat(response);
                $scope.reg.country = $scope.vars.countries[0];
                $scope.vars.stage_1_loaded = true;
            });

        $http.get("/js/prefixes.json")
            .success(function(response) {
                $scope.vars.prefixes = $scope.vars.prefixes.concat(response);
                $scope.reg.phone_prefix = $scope.vars.prefixes[0];
                $scope.vars.stage_2_loaded = true;
            });

        $http({method: 'POST', url: '/ajax/getCartData/'})
            .success(function (response) {
                if (response != 0) {
                    $scope.cart = response.cart;
                    $scope.vars.min_period = response.min_period;
                    $scope.vars.selected_period = response.min_period;
                    $scope.vars.stage_3_loaded = true;
                    if ($scope.vars.user_logged) {
                        $scope.update_userinfo();
                    } else {
                        $scope.sums = priceManager.calculateTotalsInOrder($scope, priceManager.findPeriod($scope.vars.selected_period));
                    }
                }
        });

        $scope.$watch(
           function () {
                return parseInt($scope.vars.selected_period);
            },

            function () {
                $scope.sums = priceManager.calculateTotalsInOrder($scope, priceManager.findPeriod($scope.vars.selected_period));
            });

        $scope.update_userinfo = function () {
            if ($scope.reg.country.value != 0 || $scope.vars.user_logged) {
                $http({method: 'POST', url: '/ajax/checkUserTaxStatus/', data: ($scope.vars.user_logged ? {user_id: $scope.vars.user_id}:{reg: $scope.reg})})
                    .success(function (response) {
                        if (response != 0) {
                            $scope.vars.user_vat_rate = response.result;
                            $scope.sums = priceManager.calculateTotalsInOrder($scope, priceManager.findPeriod($scope.vars.selected_period));
                        }
                });
            }
        };

        $scope.myHTMLinAdditional = function (data) {
            return $sce.trustAsHtml('<span class="text-success server-additional-price">+ ' + data.price + '/' + txt[lng].mo + '</span>' + data.name);
        };

        $scope.myHTMLinPeriods = function (period) {
            var sums = priceManager.calculateTotalsInOrder($scope, period);

            return $sce.trustAsHtml(
                        '<div class="payment-col payment-period-value">' + period.period + ' ' + txt[lng].month + ' x <strong>$&nbsp;' + sums.total_mo.toFixed(2) + '</strong><br/>(' + txt[lng].setup + ': $&nbsp;' + sums.total_setup.toFixed(2) +')</div>' +
                        '<div class="payment-col payment-total">' +
                            '<div class="payment-total-value"><strong>$&nbsp;' + sums.total_sum.toFixed(2) + '</strong></div>' +
                            '<div class="payment-total-save">' + txt[lng].save + ': $&nbsp;' + sums.discount.toFixed(2) + '</div>' +
                        '</div>'
                    );
        };

        $scope.change_status = function (status) {
            $scope.reg.status = status;
            $scope.update_userinfo();
        };

        $scope.country_prefix = function () {
            for (var i in $scope.vars.prefixes) {
               if ($scope.vars.prefixes[i].value == $scope.reg.country.code) {
                    $scope.reg.phone_prefix = $scope.vars.prefixes[i];
               }
            }
           $scope.update_userinfo();
        };

        $scope.validate_email = function () {
            $http({method: 'POST', url: '/ajax/validateEmail/', data: {email: $scope.reg.email_address }})
                .success(function (response) {
                    if (response != 0) {
                        $scope.vars.email_in_validation = true;
                    }
                });
        };

        $scope.validate_code = function () {
            $http({method: 'POST', url: '/ajax/validateCode/', data: {validation_code: $scope.vars.email_validation_code}})
                .success(function (response) {
                    if (response != 0) {
                        $scope.vars.email_validated = true;
                        $scope.vars.email_in_validation = false;
                    }
                });
        };

        $scope.change_quantity = function (item, increment) {
            if (increment === 1 && item.quantity < 10) {
                $http({method: 'POST', url: '/ajax/changeCartItemQuantity/', data: {cart_item_id: item.cart_item_id, number: item.quantity + 1}})
                    .success(function (response) {
                        if (response != 0 && response.errorcode == 1) {
                            item.quantity = item.quantity + 1;
                            $('#top-items-count a').html(response.cart_count.count + ' ' + response.cart_count.products_declension);
                            $('#btn-items-count').html(response.cart_count.count);
                        }
                    });
            } else if (increment === -1 && item.quantity > 1) {
                $http({method: 'POST', url: '/ajax/changeCartItemQuantity/', data: {cart_item_id: item.cart_item_id, number: item.quantity - 1}})
                    .success(function (response) {
                        if (response != 0 && response.errorcode == 1) {
                            item.quantity = item.quantity - 1;
                            $('#top-items-count a').html(response.cart_count.count + ' ' + response.cart_count.products_declension);
                            $('#btn-items-count').html(response.cart_count.count);
                        }
                    });
            }
        };

        $scope.pay_order = function (form) {

            if (form !== 0 && !$scope.vars.email_validated) {
                angular.forEach(form.$error, function (f) {
                    angular.forEach(f, function(field){
                        field.$setTouched();
                    });
                });
            }

            if (form !== 0 && !$scope.vars.email_validated) {
                angular.element('.validate-button').addClass('shake-button');
                $timeout( function(){ angular.element('.validate-button').removeClass('shake-button'); }, 500);

                return;
            }

            if (form !== 0 && form.$valid && $scope.vars.email_validated) {
                $http({method: 'POST', url: '/ajax/payOrder/', data: {reg: $scope.reg, payment_method: $scope.vars.payment_method, payment_period:  $scope.vars.selected_period}})
                    .success(function (response) {
                        if (response.errorcode == 0) {
                            $window.location.href = response.result.link;
                        }
                    });
            } else {
                if (form === 0) {
                    $http({method: 'POST', url: '/ajax/payOrder/', data: {payment_method: $scope.vars.payment_method, payment_period:  $scope.vars.selected_period}})
                        .success(function (response) {
                            if (response.errorcode == 0) {
                                $window.location.href = response.result.link;
                            }
                        });
                }
            }
        };

    });

    app.controller('WaitController', function ($http, $scope, $window, $timeout) {
        $scope.payment_check_tick = 6;
        $scope.callAtTimeout = function() {
            $scope.payment_check_tick--;
            if (payment_check_tick <= 0) {
                $http({method: 'POST', url: '/ajax/checkPaymentComplete/'})
                    .success(function (response) {
                        if (response.errorcode == 0) {
                            if (response.result > 1) {
                                $window.location.reload(true);
                            } else if (response.result == 1) {
                                $timeout( function(){ $scope.callAtTimeout(); }, 3000);
                            } else { // We get 0 means cancel check
                                $window.location.reload(true);
                            }
                        }
                    });
            } else {
                $window.location.reload(true);
            }
        };
        $timeout( function(){ $scope.callAtTimeout(); }, 3000);
    });

    app.controller('LoginController', function ($http, $scope, $window) {
        $scope.vars = {
            'login' : '',
            'password' : '',
            'email' : '',
            'email_sent' : false
        };

        $scope.forgot_password = function () {
            if ($scope.vars.email == '') {
                return;
            }

            $http({method: 'POST', url: '/ajax/forgotPassword/', data: {'email': $scope.vars.email}})
                .success(function (response) {
                    if (response != 0) {
                        $scope.vars.email_sent = true;
                    } else {
                        jacked_error.log(txt[lng].error);
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };

    });

    app.controller('FooterController', function ($http, $scope, $attrs) {
        if ($attrs.subscribed != '') {
            $scope.email = $attrs.subscribed;
            $scope.subscribed = true;
        } else {
            $scope.email = '';
            $scope.subscribed = false;
        }

        $scope.submit_subscribe = function () {
            if ($scope.email == '') {
                return;
            }

            $http({method: 'POST', url: '/ajax/submitSubscribe/', data: {'email': $scope.email}})
                .success(function (response) {
                    if (response == 1) {
                        $scope.subscribed = true;
                    } else {
                        jacked_error.log(txt[lng].error);
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };
    });

    app.controller('ContactController', function ($http, $scope) {

        $scope.ask = {
            department : '0',
            location : '0',
            first_name : '',
            last_name : '',
            email_address : '',
            phone : '',
            question : '',
            callback : false,
            check_img : ''
        };

        $scope.asked = false;

        $scope.submit_question = function () {
            if ($scope.ask.department == 0 || $scope.ask.location == 0) {
                jacked_error.log(txt[lng].ask_choose);
                return;
            }

            $http({method: 'POST', url: '/ajax/submitQuestion/', data: $scope.ask})
                .success(function (response) {
                    if (response != 0) {
                        if (response == 1) {
                            $scope.asked = true;
                        }
                        if (response == 2) {
                            jacked_error.log(txt[lng].ask_errorcode);
                        }
                    } else {
                        jacked_error.log(txt[lng].error);
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };
    });

    app.controller('CommentController', function ($http, $scope, $attrs) {
        $scope.comm = {
            author : '',
            email : '',
            comment : '',
            articleId : $attrs.articleId,
            userId : $attrs.userId,
            lastId : 0
        };

        $scope.vars = {
            reply_open : false,
            commented : false,
            rep_commented : []
        };

        $scope.rep = {
            author : '',
            email : '',
            comment : '',
            articleId : $attrs.articleId,
            parentId : 0,
            userId : $attrs.userId,
            lastId : 0
        };

        $scope.submit_comment = function () {
            $http({method: 'POST', url: '/ajax/addNewFaqComment/', data: {comment: $scope.comm}})
                .success(function (response) {
                    if (response != 0) {
                        $scope.vars.commented = true;
                    } else {
                        jacked_error.log(txt[lng].error);
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };

        $scope.submit_reply = function () {
            if ($scope.vars.reply_open) {
                $scope.rep.parentId = $scope.vars.reply_open;

                $http({method: 'POST', url: '/ajax/addFaqReply/', data: {reply: $scope.rep}})
                    .success(function (response) {
                        if (response != 0) {
                            $scope.vars.rep_commented.push($scope.vars.reply_open);
                            $scope.rep.comment = '';
                        } else {
                            jacked_error.log(txt[lng].error);
                        }
                    })
                    .error(function () {
                        jacked_error.log(txt[lng].error);
                    });
            }
        };

    });

    app.controller('DashOverviewController', function ($http, $scope, $timeout) {
        $scope.user_info = {
            'language' : '',
            'sms_notify' : '',
            'auto_pay' : '',
            'disabled' : false,
        };

        $scope.change_user_info = function () {
            $scope.user_info.disabled = true;

            var data = angular.copy($scope.user_info);
            data.auto_pay = $scope.user_info.auto_pay ? 1 : 0;
            data.sms_notify = $scope.user_info.sms_notify ? 1 : 0;

            $http({method: 'POST', url: '/dashboard/ajaxChangeUserInfo/', data: {'user_info': data}})
                .success(function (response) {
                    if (response == 1) {
                        jacked_success.log(txt[lng].db_saved);
                        $timeout( function(){ $scope.user_info.disabled = false; }, 3000);

                    } else {
                        jacked_error.log(txt[lng].error);
                        $timeout( function(){ $scope.user_info.disabled = false; }, 3000);
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                    $timeout( function(){ $scope.user_info.disabled = false; }, 3000);
                });
        };

    });

    app.controller('DashServersController', function ($http, $scope, $sce) {

        $scope.vars = {
            selected_server : [],
            selected_server_graphs : [],
            selected_server_info : [],
            selected_tab : '',
            due_invoices : '',
            overdue_invoices : '',
            tickets_count : '',
            servers_count : '',
            selected_os_caption : '',
            selected_os : 0,
            selected_os_arch : 0,
            selected_os_x32 : 0,
            selected_os_x64 : 0,
            new_root_password : '',
            new_root_confirm_password : '',
            current_password : ''
        };

        $scope.forms = {};

        $scope.get_server_info = function (t) {
            if ($scope.vars.selected_server[t] != '') {
                $http({method: 'POST', url: '/dashboard/ajaxGetServerInfo/', data: {'selected_server': $scope.vars.selected_server[t]}})
                    .success(function (response) {
                        if (response != 0) {
                            if (response.graph_html != '') {
                                $scope.vars.selected_server_graphs[t] = response.graph_html;
                            } else {
                                $scope.vars.selected_server_graphs[t] = '<div id="no-data">' + txt[lng].db_no_data + '</div>';
                            }
                            $scope.vars.selected_server_info[t] = response.data;
                            $scope.vars.due_invoices = response.due;
                            $scope.vars.tickets_count = response.tickets_count;
                            $scope.vars.servers_count = response.servers_count;
                            $scope.vars.overdue_invoices = response.overdue;
                        } else {
                            jacked_error.log(txt[lng].error);
                        }
                    })
                    .error(function () {
                        jacked_error.log(txt[lng].error);
                    });
            }
        };

        $scope.trafficUsage = function (t) {
            return $sce.trustAsHtml($scope.vars.selected_server_graphs[t]);
        };

        $scope.set_selected_server = function (t, s) {
            $scope.vars.selected_server[t] = s;
            $scope.get_server_info(t);
        };

        $scope.change_tab = function (t) {
            $scope.vars.selected_tab = t;
        };

        $scope.change_install_os = function () {
            $scope.vars.selected_os_x32 = parseInt(angular.element('#os-selector')[0].selectedOptions[0].dataset.x32bit);
            $scope.vars.selected_os_x64 = parseInt(angular.element('#os-selector')[0].selectedOptions[0].dataset.x64bit);
            $scope.vars.selected_os_caption = angular.element('#os-selector')[0].selectedOptions[0].dataset.caption;

            if ($scope.vars.selected_os_arch == 64 && !$scope.vars.selected_os_x64) {
                $scope.vars.selected_os_arch = 32;
            }

            if ($scope.vars.selected_os_arch == 32 && !$scope.vars.selected_os_x32) {
                $scope.vars.selected_os_arch = 64;
            }
        };

        $scope.server_reinstall = function () {
            $http({method: 'POST', url: '/dashboard/ajaxServerReinstall/',
                data: {
                    'selected_server': $scope.vars.selected_server[$scope.vars.selected_tab],
                    'root_password': $scope.vars.new_root_password,
                    'current_password': $scope.vars.current_password,
                    'selected_os': $scope.vars.selected_os,
                    'selected_os_arch': $scope.vars.selected_os_arch
                }})
                .success(function (response) {
                    if (response != 0) {
                        jacked_success.log(txt[lng].db_request_accd);
                        $('#server-reinstall .md-close').trigger('click');
                    } else {
                        jacked_error.log(txt[lng].error);
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };

        $scope.server_reboot = function () {
            $http({method: 'POST', url: '/dashboard/ajaxServerReboot/', data: {'selected_server': $scope.vars.selected_server[$scope.vars.selected_tab]}})
                .success(function (response) {
                    if (response != 0) {
                        jacked_success.log(txt[lng].db_request_accd);
                        $('#server-reboot .md-close').trigger('click');
                    } else {
                        jacked_error.log(txt[lng].error);
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };
    });

    app.controller('DashBillingController', function ($http, $scope, $window) {
        var today = new Date();
        var from_date = new Date(today);
        from_date.setMonth(from_date.getMonth() - 2);

        $scope.Math = window.Math;

        $scope.check_loaded = function() {
            return ($scope.vars.stage_1_loaded && $scope.vars.stage_2_loaded);
        };

        $scope.switch_tab = function (e) {
            window.location.hash = e;
        };

        $scope.is_active = function (h) {
            var hash = document.location.hash;
            if (hash === "#unpaid" || hash === "#history") {
                return (h === hash);
            } else {
                return (h === "#unpaid");
            }
        };

        $scope.today_from = function() {
            $scope.vars.dt_from = new Date();
        };

        $scope.clear_from = function () {
            $scope.vars.dt_from = null;
        };

        $scope.open_from = function($event) {
            $event.preventDefault();
            $event.stopPropagation();

            $scope.opened_from = $scope.opened_from ? false : true;
        };

        $scope.today_to = function() {
            $scope.vars.dt_to = new Date();
        };

        $scope.clear_to = function () {
            $scope.vars.dt_to = null;
        };

        $scope.open_to = function($event) {
            $event.preventDefault();
            $event.stopPropagation();

            $scope.opened_to = $scope.opened_to ? false : true;
        };

        $scope.format = 'dd/MM/yyyy';

        $scope.dateOptions = {
            showWeeks : false,
            formatYear: 'yy',
            startingDay: 1
        };

        $scope.forms = {};

        $scope.vars = {
            'dt_from' : new Date(from_date.getFullYear(), from_date.getMonth(), 1),
            'dt_to' : today,
            'max_date' : today,
            'sum_bonus_txt' : 0,
            'sum_selected' : 0.0,
            'sum_bonus' : 0.0,
            'sum_total' : 0.0,
            'payment_method' : 'visa',
            'selected_documents' : [],
            'stage_1_loaded' : false,
            'stage_2_loaded' : false,
            'pay_status' : 0,
            'pay_button' : 'btn-default',
            'pay_button_text' : txt[lng].db_btn_pay,
            'filter_status' : 0,
            'filter_button' : 'btn-default',
            'filter_button_text' : txt[lng].db_btn_fltr
        };

        $scope.documents = {};
        $scope.history = {};

        $scope.apply_filter = function() {
            $http({method: 'POST', url: '/dashboard/ajaxGetBillingHistory/', data: {'from': $scope.vars.dt_from, 'to': $scope.vars.dt_to}})
                .success(function (response) {
                    if (response != 0) {
                        $scope.history = response;
                    } else {
                        $scope.history = {};
                    }
                    $scope.vars.stage_2_loaded = true;
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });

        };

        $http({method: 'POST', url: '/dashboard/ajaxGetUnpaidDocuments/'})
            .success(function (response) {
                if (response != 0) {
                    $scope.documents = response;
                }
                $scope.vars.stage_1_loaded = true;
            })
            .error(function () {
                jacked_error.log(txt[lng].error);
            });

        $scope.apply_filter();

        $scope.$watch(
            function () {
                return parseFloat($scope.vars.sum_selected) + parseFloat($scope.vars.sum_bonus_txt);
            },

            function () {
                if (parseFloat($scope.vars.sum_bonus_txt) > 0) {
                    $scope.vars.sum_bonus = parseFloat($scope.vars.sum_bonus_txt);
                } else {
                    $scope.vars.sum_bonus = 0.0;
                }
                $scope.vars.sum_total = parseFloat($scope.vars.sum_selected) + $scope.vars.sum_bonus;
            });

        $scope.select_documents = function () {
            $scope.vars.sum_selected = 0.0;
            $scope.vars.selected_documents = [];

            if ($scope.documents !== undefined) {
                for (var document in $scope.documents) {
                    if ($scope.documents[document].selected) {
                        $scope.vars.sum_selected += ($scope.documents[document].total - $scope.documents[document].linked);
                        $scope.vars.selected_documents.push({'type':$scope.documents[document].type, 'document_id':$scope.documents[document].idInvoice});
                    }
                }
            }
        };

        $scope.pay = function () {
            var data = {
                'selected_documents' : $scope.vars.selected_documents,
                'payment_method' : $scope.vars.payment_method,
                'amount' : $scope.vars.sum_total
            };

            $http({method: 'POST', url: '/dashboard/ajaxGetPaymentForm/', data: {'payment_data': data}})
                .success(function (response) {
                    if (response != 0) {
                        if (response.url_string !== undefined) {
                            $window.location.href = response.url_string;
                        }

                        if (response.form !== undefined) {
                            $(".content-section").append(response.form);
                            $("#payform").submit();
                        }
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };

    });

    app.controller('DashSettingsController', function ($http, $scope) {
        $scope.forms = {};

        $scope.vars = {
            'countries' : [
                { "code": 0, "value": "0", "label": txt[lng].select_country }
            ],
            'prefixes' : [
                { "value": 0, "label": txt[lng].select_prefix }
            ],
            'stage_1_loaded' : false,
            'stage_2_loaded' : false,
            'stage_3_loaded' : false,
            'stage_4_loaded' : false,
            'info_changed' : false,
            'password_status' : 0,
            'password_button' : 'btn-default',
            'password_button_text' : txt[lng].db_btn_pwd,
            'info_status' : 0,
            'info_button' : 'btn-default',
            'info_button_text' : txt[lng].db_btn_subm
        };

        $scope.user_info_copy = {};

        $scope.user_info = {
            'gender' : 0,
            'first_name' : '',
            'last_name' : '',
            'company_name' : '',
            'vat' : '',
            'address' : '',
            'zip' : '',
            'city' : '',
            'country' : $scope.vars.countries[0],
            'phone_prefix' : $scope.vars.prefixes[0],
            'phone' : '',
            'email' : '',
            'position' : 0
        };

        $scope.user_pass = {
            'password' : '',
            'confirm_password' : ''
        };

        $scope.$watchCollection(
            'user_info',

            function () {
                if ($scope.vars.stage_1_loaded &&
                    $scope.vars.stage_2_loaded &&
                    $scope.vars.stage_3_loaded &&
                    $scope.vars.stage_4_loaded &&
                    $scope.forms.settingsForm.$dirty) {
                    if (!angular.equals($scope.user_info_copy, $scope.user_info) && $scope.forms.settingsForm.$valid) {
                        $scope.vars.info_changed = true;
                    } else {
                        $scope.vars.info_changed = false;
                    }
                } else {
                    $scope.user_info_copy = angular.copy($scope.user_info);
                }
            });

        $http.get("/js/countries.json")
            .success(function(response) {
                $scope.vars.countries = $scope.vars.countries.concat(response);
                $scope.user_info.country = $scope.vars.countries[0];
                $scope.vars.stage_1_loaded = true;
            });

        $http.get("/js/prefixes.json")
            .success(function(response) {
                $scope.vars.prefixes = $scope.vars.prefixes.concat(response);
                $scope.user_info.phone_prefix = $scope.vars.prefixes[0];
                $scope.vars.stage_2_loaded = true;
            });

        $scope.find_selected_country = function (selected) {
            $scope.vars.stage_3_loaded = true;
            for (var country in $scope.vars.countries) {
                if (selected == $scope.vars.countries[country].value) {
                    return $scope.vars.countries[country];
                }
            }
            return $scope.vars.countries[0];
        };

        $scope.find_selected_phone_prefix = function (selected) {
            $scope.vars.stage_4_loaded = true;
            for (var prefix in $scope.vars.prefixes) {
                if (selected == $scope.vars.prefixes[prefix].value) {
                    return $scope.vars.prefixes[prefix];
                }
            }
            return $scope.vars.prefixes[0];
        };

        $scope.country_prefix = function () {
            for (var i in $scope.vars.prefixes) {
               if ($scope.vars.prefixes[i].value == $scope.user_info.country.code) {
                    $scope.user_info.phone_prefix = $scope.vars.prefixes[i];
               }
            }
        };

        $scope.change_info = function () {
            var data = angular.copy($scope.user_info);
            data.country = $scope.user_info.country.value;
            data.phone_prefix = $scope.user_info.phone_prefix.value;
            $scope.vars.info_status = 3;

            $http({method: 'POST', url: '/dashboard/ajaxChangeUserInfo/', data: {'user_info': data}})
                .success(function (response) {
                    if (response == 1) {
                        $scope.vars.info_status = 1;
                        $scope.vars.info_button = 'btn-success';
                        $scope.vars.info_button_text = txt[lng].db_btn_rsent;
                    } else {
                        $scope.vars.info_status = 2;
                        $scope.vars.info_button = 'btn-warning';
                        $scope.vars.info_button_text = txt[lng].db_btn_erch;
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };

        $scope.change_password = function () {
            var data = angular.copy($scope.user_pass);
            $scope.vars.password_status = 3;

            $http({method: 'POST', url: '/dashboard/ajaxChangeUserInfo/', data: {'user_info': data}})
                .success(function (response) {
                    if (response == 1) {
                        $scope.vars.password_status = 1;
                        $scope.vars.password_button = 'btn-success';
                        $scope.vars.password_button_text = txt[lng].db_btn_pchd;
                    } else {
                        $scope.vars.password_status = 2;
                        $scope.vars.password_button = 'btn-warning';
                        $scope.vars.password_button_text = txt[lng].db_btn_epch;
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };

        $scope.reset_password = function () {
            if ($scope.vars.password_status != 0) {
                $scope.user_pass.password = '';
                $scope.user_pass.confirm_password = '';
                $scope.vars.password_status = 0;
                $scope.vars.password_button = 'btn-default';
                $scope.vars.password_button_text = txt[lng].db_btn_pwd;
            }
        };

    });

    app.controller('DashSecurityController', function ($http, $scope) {
        $scope.vars = {
            'stage_1_loaded' : false,
        };

        $scope.pagination = {
            'page' : 0,
            'count' : 10,
            'total' : 0
        };

        $scope.user_logins = [];

        $scope.get_user_logins_page = function () {
            $http({method: 'POST', url: '/dashboard/ajaxGetUserLogins/', data: $scope.pagination})
                .success(function (response) {
                    if (response != 0) {
                        $scope.pagination.total = response.total;
                        $scope.user_logins = response.current_page;
                        $scope.vars.stage_1_loaded = true;

                    } else {
                        jacked_error.log(txt[lng].error);
                    }
                })
                .error(function () {
                    jacked_error.log(txt[lng].error);
                });
        };

        $scope.get_user_logins_page();
    });

    app.controller('DashSupportController', function ($http, $scope) {
        $scope.loaded = false;
        $scope.vars = {};


        $scope.init = function(status, currentTicketId) {
            $http.get('/dashboard/ajaxSupport/')
                .success(function (response) {
                    if (response !== 1) {
                        $scope.vars.type = 'other';
                        $scope.vars.message = '';
                        $scope.vars.answer = false;
                        $scope.vars.answerMessage = '';

                        $scope.vars.tickets = {
                            open: [],
                            pending: [],
                            closed: []
                        };

                        for (var ticket in response.tickets) {
                            if (
                                parseInt(response.tickets[ticket].done) === 0 &&
                                parseInt(response.tickets[ticket].response) === 1 &&
                                parseInt(response.tickets[ticket].deleted) === 0
                            ) {
                                $scope.vars.tickets.open.push(response.tickets[ticket]);
                            } else if (
                                parseInt(response.tickets[ticket].done) === 0 &&
                                parseInt(response.tickets[ticket].response) === 0 &&
                                parseInt(response.tickets[ticket].deleted) === 0
                            ) {
                                $scope.vars.tickets.pending.push(response.tickets[ticket]);
                            } else if (
                                parseInt(response.tickets[ticket].done) === 1 &&
                                parseInt(response.tickets[ticket].deleted) === 0
                            ) {
                                $scope.vars.tickets.closed.push(response.tickets[ticket]);
                            }
                        }

                        $scope.switchTicketView(status);

                        if (currentTicketId !== 0) {
                            for (var ticket in response.tickets) {
                                if (parseInt(currentTicketId) === parseInt(response.tickets[ticket].ticket_id)) {
                                    $scope.vars.currentTicket = response.tickets[ticket];
                                }
                            }
                        }

                        $scope.vars.servers = [];
                        $scope.vars.servers.push(
                            {
                                id: 0,
                                name: txt[lng].db_tckt_noissue
                            });

                        for (var group in response.servers) {
                            for (var server in response.servers[group].items) {
                                $scope.vars.servers.push(
                                    {
                                        id: response.servers[group].items[server].server.id,
                                        name: response.servers[group].items[server].server.internal_name +
                                            ((response.servers[group].items[server].server.custom_name !== '') ? '(' + response.servers[group].items[server].server.custom_name + ')' : '')
                                    });
                            }
                        }

                        $scope.vars.server = $scope.vars.servers[0];
                        $scope.loaded = true;
                    }
                });
        }

        $scope.switchTicketView = function (status) {
            if (status === 'open') {
                $scope.vars.open = true;
                $scope.vars.pending = false;
                $scope.vars.closed = false;

                if (($scope.vars.tickets.open).length > 0) {
                    $scope.vars.currentTicket = $scope.vars.tickets.open[0];
                }
            } else if (status === 'pending') {
                $scope.vars.open = false;
                $scope.vars.pending = true;
                $scope.vars.closed = false;

                if (($scope.vars.tickets.pending).length > 0) {
                    $scope.vars.currentTicket = $scope.vars.tickets.pending[0];
                }
            } else if (status === 'closed') {
                $scope.vars.open = false;
                $scope.vars.pending = false;
                $scope.vars.closed = true;

                if (($scope.vars.tickets.closed).length > 0) {
                    $scope.vars.currentTicket = $scope.vars.tickets.closed[0];
                }
            }
        };

        $scope.switchCurrentTicket = function (ticket) {
            $scope.vars.currentTicket = ticket;
        };

        $scope.submitTicket = function() {
            if (($scope.vars.message).length > 5) {

            var data = {
                server_id: $scope.vars.server.id,
                type: $scope.vars.type,
                message: $scope.vars.message
            };

            $http({method: 'POST', url: '/ajax/submitTicket/', data: data})
                .success(function (response) {
                    if (response !== 1) {

                        $scope.loaded = false;
                        $scope.init('pending', $scope.vars.currentTicket.ticket_id);
                    }
                })
            } else {
                jacked_error.log(txt[lng].db_tckt_short);
            }
        };

        $scope.closeTicket = function(status) {
            var data = {
                ticket_id: $scope.vars.currentTicket.ticket_id
            };
            $http({method: 'POST', url: '/ajax/closeTicket/', data: data})
                .success(function (response) {
                    if (response !== 1) {

                        $scope.loaded = false;
                        $scope.init(status, 0);
                    }
                });
        };

        $scope.answerTicket = function() {
            $scope.vars.answer = true;
        };

        $scope.submitAnswerTicket = function (status) {
            if (($scope.vars.answerMessage).length > 5) {
                var data = {
                    ticket_id: $scope.vars.currentTicket.ticket_id,
                    message: $scope.vars.answerMessage,
                    team: 0
                };

                $http({method: 'POST', url: '/ajax/answerTicket/', data: data})
                    .success(function (response) {
                        if (response !== 1) {

                            $scope.loaded = false;
                            $scope.init(status, $scope.vars.currentTicket.ticket_id);
                        }
                    });
            } else {
                jacked_error.log(txt[lng].db_tckt_short);
            }
        }

        $scope.uploadFile = function() {

        };

        $scope.reopenTicket = function() {
            var data = {
                ticket_id: $scope.vars.currentTicket.ticket_id
            };
            $http({method: 'POST', url: '/ajax/reopenTicket/', data: data})
                .success(function (response) {
                    if (response !== 1) {

                        $scope.loaded = false;
                        $scope.init('closed', 0);
                    }
                });
        };

        $scope.deleteTicket = function() {
            var data = {
                ticket_id: $scope.vars.currentTicket.ticket_id
            };
            $http({method: 'POST', url: '/ajax/deleteTicket/', data: data})
                .success(function (response) {
                    if (response !== 1) {

                        $scope.loaded = false;
                        $scope.init('closed', 0);
                    }
                });
        };

        $scope.getFile = function() {

        };

        $scope.init('pending', 0);
    });

    app.service('priceManager', function (periods) {
        this.calculateTotalsInOrder = function(scope, period) {
            var total_mo = 0;
            var total_setup = 0;

            for (var i in scope.cart) {
                total_mo += parseFloat(scope.cart[i].complete_monthly_fee) * parseInt(scope.cart[i].quantity);
                total_setup += parseFloat(scope.cart[i].complete_setup_fee) * parseInt(scope.cart[i].quantity);
            }

            var total_sum = (total_setup + total_mo * period.period) * ((100 - period.discount) / 100);
            var discount = (total_setup + total_mo * period.period) * (period.discount / 100);

            if (scope.vars.user_vat_rate > 0) {
                var vat = total_sum * scope.vars.user_vat_rate / 100;
                var total_sum_vat = total_sum * (1 + scope.vars.user_vat_rate / 100);
            } else {
                var vat = 0;
                var total_sum_vat = total_sum;
            }

            return {
                'total_sum' : total_sum,
                'total_sum_vat' : total_sum_vat,
                'discount' : discount,
                'total_mo' : total_mo,
                'total_setup' : total_setup,
                'vat' : vat
            };
        };

        this.calculateTotals = function(scope, period, quantity) {
            var old_setup = parseFloat(scope.srv.old_setup_fee),
                total_setup = parseFloat(scope.srv.setup_fee),
                total_mo = parseFloat(scope.srv.monthly_fee);

            for (var prod in scope.selected_products) {
                total_setup += parseFloat(scope.selected_products[prod].setup_fee);
                total_mo += parseFloat(scope.selected_products[prod].price);
            }

            total_setup = total_setup * quantity;
            total_mo = total_mo * quantity;

            var total_sum = (total_setup + total_mo * period.period) * ((100 - period.discount) / 100);
            var discount = (total_setup + total_mo * period.period) * (period.discount / 100);

            return {
                'old_setup' : old_setup,
                'total_sum' : total_sum,
                'discount' : discount,
                'total_mo' : total_mo,
                'total_setup' : total_setup
            };
        };

        this.findPeriod = function(period) {
            for (var p in periods) {
                if (periods[p].period == period) {
                    return periods[p];
                }
            }
        };
    });

    app.filter('unsafe', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);
        };
    });

    app.filter('isDefaultProduct', function () {
        return function (items, isdef) {
            var filtered = [];

            for (var i in items) {
                if (items[i].is_default_product == isdef) {
                    filtered.push(items[i]);
                }
            }

            return filtered;
        };
    });

    app.filter('crossFilter', function () {
        return function (products, subcat, cross_dep, selected, scope) {
            if (cross_dep.detailed.length === 0 || cross_dep.optimized.indexOf(subcat) === -1) {
                return products;
            }

            var filtered = [];

            for (var i in products) {
                for (var j in cross_dep.detailed) {
                    if (cross_dep.detailed[j].dependent_cat == subcat && cross_dep.detailed[j].dependent_prod == products[i].product_id) {
                        for (var k in selected) {
                            if (selected[k].sub_category_id == cross_dep.detailed[j].condition_cat && selected[k].product_id == cross_dep.detailed[j].condition_prod) {
                                filtered.push(products[i]);
                            }
                        }
                    }
                }
            }

            if (filtered.length === 0) {
                return products;
            }

            var selected_index = selected.map(function(e){return e.sub_category_id}).indexOf(subcat);
            var selected_product = selected.map(function(e){return e.id})[selected_index];

            if (filtered.map(function(e){return e.id}).indexOf(selected_product) === -1) {
                selected[selected_index] = filtered[0];
                scope.selected_prod = filtered[0];
            }

            return filtered;
        };
    });

    app.filter('isInCategory', function () {
        return function (items, cat) {
            var filtered = [];

            for (var i in items) {
                if (items[i].category_id == cat) {
                    filtered.push(items[i]);
                }
            }

            return filtered;
        };
    });

    app.directive('select', function ($timeout) {
        return {
            restrict: 'E',
            require: ['?select', '?ngModel'],

            link: {
                post: function (scope, el, attr, model) {
                    if (!model || model.length < 2) {
                        return;
                    }

                    if (el.hasClass('selectric-with-icons')) {
                        $timeout( function(){
                            el.selectric({
                                optionsItemBuilder: function(itemData, element, index) {
                                  return element.val().length ? '<img width="20" height="20" style="margin-right:10px;" src="' + element[0].dataset.selectricIcon +  '"/> ' + itemData.text : itemData.text;
                                },
                                preventWindowScroll: true,
                                responsive: !0
                            });
                        }, 0);
                    } else {
                        $timeout( function(){
                            el.selectric({
                                preventWindowScroll: true,
                                responsive: !0
                            });
                        }, 0);
                    }

                    scope.$watch(
                        function () {
                            return el[0].length;
                        },

                        function () {
                            $timeout( function(){el.selectric('refresh')}, 0);
                        });

                    scope.$watch(
                        function () {
                            return attr.disabled;
                        },

                        function () {
                            $timeout( function(){el.selectric('refresh')}, 0);
                        });

                    scope.$on('$destroy', function () {
                        el.selectric('destroy');
                    });

                    scope.$watch(
                        function () {
                            return el[0].selectedIndex;
                        },

                        function () {
                            $timeout( function(){el.selectric('refresh')}, 0);
                        });

                }
            }
        };
    });

    app.directive('compareTo', function () {
        return {
            require: 'ngModel',
            scope: {
                ov: '=compareTo'
            },
            link: function(scope, el, attr, model) {
                model.$parsers.unshift(function (vv) {
                    if (vv == scope.ov) {
                        model.$setValidity('compareTo', true);
                        return vv;
                    } else {
                        model.$setValidity('compareTo', false);
                        return undefined;
                    }
                });
            }
        };
    });

    /*
     * Preloader
     */
    app.directive('preloader', function() {
        return {
            restrict: "E",
            template: '<img src="/img/preloader.gif">'
        };
    });

})();
