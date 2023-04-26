/**
 * Webkul    viewonproduct js.
 * @category Webkul
 * @package  Webkul_ZipCodeValidator
 * @author   Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
define([
    "jquery",
    'mage/translate',
	'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert',
    "mage/mage",
], function ($, $t, alert, modal) {
    'use strict';    
    $.widget('fahim.viewonproduct', {
        options: {},
        _create: function () {
            var self = this;
            $(document).ready(function () {
                var baseurl = self.options.url;
                var currenturl = self.options.currenturl;
                var isCustomerActive = self.options.isCustomerActive;
                var ajax;
                var prevzip;
                var count = 0;
                var zipcode;
                var prevstatus;
                var prevseller;
                
                $('[class^=wk-zcv-zipform]').keydown(function (e) {
                    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                        return;
                    }
                    if ((e.keyCode < 48 && e.keyCode > 90) || (e.keyCode < 96 && e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });

                $('[class^=wk-zcv-zipform]').keydown(function (event) {
                    var keyCode = (event.keyCode ? event.keyCode : event.which);
                    if (keyCode == 13) {
                        event.preventDefault();
                        $('[id^=wk-zcv-check]').trigger('click');
                    }
                });

                $('[class^=wk-zcv-zipform]').change(function (event) {
                    var sellerId = $(this).attr('seller-data-id');
                    zipcode = $('body .wk-zcv-zipform'+sellerId).val();
                    console.log("zipcode", zipcode);
                    
                    if (zipcode) {
                        zipcode = zipcode.replace(/(<([^>]+)>)/ig,"");
                        $('.wk-zcv-zipform'+sellerId).val(zipcode);
                    }
                });

                $(document).on('click', '[id^=wk-zcv-check]', function (event) {
                    count++;
                    var productId =  $(this).attr('data-pro-id');
                    var sellerId = $(this).attr('data-id');
                    zipcode = $('.wk-zcv-zipform'+sellerId).val();
                    if (zipcode) {
                        zipcode = zipcode.replace(/(<([^>]+)>)/ig,"");
                        $('.wk-zcv-zipform'+sellerId).val(zipcode);
                    }
                    
                    if (zipcode != prevzip || prevseller != sellerId) {
                        $('.wk-zcv-zipcookie'+sellerId).hide();
                        if (zipcode != '') {
                            $('.wk-zcv-ziperror'+sellerId).css('display','none');
                            $('.wk-zcv-zipsuccess'+sellerId).css('display','none');
                            $('.wk-zcv-loader'+sellerId).css('display','block');
                            callAjax(zipcode, productId, sellerId);
                            prevzip = zipcode;
                            prevseller = sellerId;
                        } else {
                            $('.wk-zcv-zipsuccess'+sellerId).css('display','none');
                            $('.wk-zcv-ziperror'+sellerId).css('display','block');
                            $('.wk-zcv-ziperror'+sellerId).html($t("Please enter a Pincode"));
                        }
                    }
                });

                $(document).on('click', '#login', function (event) {
                    $.ajax({
                        url : currenturl,
                        type : "GET",
                        complete : function (response) {
                            $('.wk-zcv-login-popup').show();
                        }
                    });
                });

                $(document).on('click', '.wk-zcv-login-popup span.close-login-popup', function (event) {
                    $('.wk-zcv-login-popup').hide();
                    });

                function callAjax(zipcode, productId, sellerId)
                {
                    var token = 0;
                    if (sellerId > 0) {
                        token = 1;
                    }
                    if (productId && zipcode) {
                        ajax = $.ajax({
                            url : baseurl + "zipcodevalidator/zipcode/result",
                            data : {
                                zip :zipcode,
                                productId : productId,
                                token : token,
                            },
                            type : "GET",
                            success : function (response) {
                                $('.wk-zcv-loader'+sellerId).css('display','none');
                                if (response.addesses) {
                                    setCookieaddress(response.addesses, sellerId);
                                }
                                setCookieUrl(sellerId);
                                if (typeof response.cookieZip != 'undefined') {
                                    $('#wk-zcv-addr'+sellerId).css('border-bottom','1px solid #ddd');
                                    setCookiezip(response.cookieZip, sellerId);
                                }
                                if (typeof response.product_id != 'undefined' && count != 1) {
				    var updated_price = response.updated_price;
                                    $('.wk-zcv-ziperror'+sellerId).css('display','none');
                                    $('.wk-zcv-zipsuccess'+sellerId).css('display','block');
                                    $('.wk-zcv-zipsuccess'+sellerId).html($t('Product is available at ')+response.product_zipcode);
								   if(response.devliverytime != undefined){
									$('#deltime').html('Estimated Delivery '+response.devliverytime+' ('+response.pincodecity+')');
								   }
									 if(updated_price != 0){
				       var updated_price_ft = response.updated_price_ft;
                                       $(".js-price span.val").css("text-decoration","line-through");
				       $(".cart-price-value").css("text-decoration","line-through");
					   $(".mrp-t9l").text("Offer Price ");
				       $("#mrp_new").text("Offer Price ");
                                       if($(".best-price-new").length==0){
                                         if($('.price_unit_select').val() == 'merte'){
					     $('<span class="best-price-new" data-feet="'+updated_price_ft+'" data-merte="'+updated_price+'" style="margin-left:10px">'+updated_price+'</span>').insertAfter(".js-price span.val");
					     	 
                                         }else{
                                             $('<span class="best-price-new" data-feet="'+updated_price_ft+'" data-merte="'+updated_price+'" style="margin-left:10px">'+updated_price_ft+'</span>').insertAfter(".js-price span.val");
                                         }
                                       }else{
                                         if($('.price_unit_select').val() == 'merte'){
                                             $(".best-price-new").text(updated_price);
					 }else{
					     $(".best-price-new").text(updated_price_ft);
                                         }
                                       }

                                        if($(".best-price-new-cart").length==0){
                                         if($('.price_unit_select').val() == 'merte'){
					     $('<span class="best-price-new-cart" data-feet="'+updated_price_ft+'" data-merte="'+updated_price+'" style="margin-left:10px; color: #fff; ">'+updated_price+'</span>').insertAfter(".cart-price-value");
						 
                                         }else{
                                             $('<span class="best-price-new-cart" data-feet="'+updated_price_ft+'" data-merte="'+updated_price+'" style="margin-left:10px; color: #fff;">'+updated_price_ft+'</span>').insertAfter(".cart-price-value");                                         
					 }
                                       }else{
                                         if($('.price_unit_select').val() == 'merte'){
                                             $(".best-price-new-cart").text(updated_price);
					 }else{
					     $(".best-price-new-cart").text(updated_price_ft);
					 }
                                       }
                                        
				       $(".js-price").attr("data-merte-best", updated_price);
                                       
                                       /* Profit Percentage */
				       
                                       var old_price = parseFloat($(".js-price").attr("data-merte"));                                        
                                       var profit = old_price - parseFloat(updated_price);
                                       var per_profit = Math.round((profit * 100)/old_price);
                                       

                                         
					/* Profit Percentage */

                                      /* Price per box */
					var tileperbox = parseFloat($(".js-price").attr("data-price-per-box"));

                                        var total_boxes_order = $("#total_boxes_order").val();

			                var tilesize = $(".js-price").attr("data-tilesize");

			                var tileperbox = parseFloat($(".js-price").attr("data-tileperbox"));

			
				
					var tileprice = updated_price;
				
			

			var result = tilesize.split('x');

			var size = (result[0] * result[1]);

			var sizeinm = size * 0.000001;

			var totalaboxperarea = total_boxes_order * sizeinm * tileperbox;

			var oneboxprice = Math.ceil(sizeinm * tileperbox * tileprice);

			var totalareaneeded = totalaboxperarea * 10.7639;

			$("#total_area_order").val(Math.round(totalareaneeded));

			$(".tilesprice").show();

			var totaltileprice = oneboxprice * total_boxes_order;

			$("#tilesprice").text('INR ' + Math.round(totaltileprice));
                                      /* Price per box*/

				      /* $(".js-price").attr("data-merte", updated_price);
				       $(".js-price").attr("data-feet", updated_price_ft); */
				       
				       var options = {
                					type: 'popup',
                					responsive: true,
                					innerScroll: true,
							modalClass: 'best-price-popup',
                					title: 'Hey, you just saved '+per_profit+'% on price. Hurry! Offer price for limited period.',
                					buttons: [{
                    						text: $.mage.__('Close'),
                    						class: '',
                    						click: function () {
                        						this.closeModal();
                    						}
                					}]
            					     };

            				var popup = modal(options, $('#popup-modal'));
            				$("#popup-modal").modal("openModal");
				    }else{
                                      var options = {
                					type: 'popup',
                					responsive: true,
                					innerScroll: true,
							modalClass: 'best-price-popup',
                					title: '<span>You have the best offer right now.</span>',
                					buttons: [{
                    						text: $.mage.__('Close'),
                    						class: '',
                    						click: function () {
                        						this.closeModal();
                    						}
                					}]
            					     };

            				var popup = modal(options, $('#popup-modal'));
            				$("#popup-modal").modal("openModal");
                                    }
									window.dataLayer.push({'event': 'checksForAvailability','pinCode': zipcode, 'status':'Available'}); 
                                } else {
                                    if (count != 1) {
                                        $('.wk-zcv-zipsuccess'+sellerId).css('display','none');
                                        $('.wk-zcv-ziperror'+sellerId).css('display','block');
                                        $('.wk-zcv-ziperror'+sellerId).html($t("Product is not available at ")+zipcode);
										
										window.dataLayer.push({'event': 'checksForAvailability','pinCode': zipcode, 'status':'Not Available'});
                                    }
                                }
                            }
                        });
                    }
                }

                function setCookieaddress(address, sellerId)
                {
                    $('#wk-zcv-addr'+sellerId+' li').remove();
                    $('#wk-zcv-addr'+sellerId).css('display','block');
                    var l = address.length;
                    for (var i=0; i<= l; i++) {
                        if (i == 0) {
                            $('#wk-zcv-addr'+sellerId).append('<li class="wk-zcv-saveaddr" seller-id="'+sellerId+'">'+$t("Saved Addresses")+'</li>');
                        }
                        if (address[i]) {
                            var addrZip = getZipCode(address[i]);
                            $('#wk-zcv-addr'+sellerId).append('<li title="'+addrZip[0]+'" seller-id="'+sellerId+'" class="wk-zcv-'+i+'">'+addrZip[0]+ '<span class="wk-zcv-address">  '+addrZip[1]+' '+addrZip[2]+'</span></li>');
                        }
                    }
                }

                function getZipCode(addr)
                {
                    var addrZip = addr.split(' ');
                    return addrZip;
                }

                function setCookieUrl(sellerId)
                {
                    $('#wk-zcv-login'+sellerId+' li').remove();
                    if (!isCustomerActive) {
                        $('#wk-zcv-login'+sellerId).css('display','block');
                        $('#wk-zcv-login'+sellerId).append(
                            '<li class="wk-zcv-log" seller-id="'+sellerId+'"><span id="login">login</span><span>'+$t(" to see your saved addresses")+'</span></li>'
                        );
                    }
                }

                function setCookiezip(cookieZip, sellerId)
                {
                    var uniquezip = [];
                    var zip = cookieZip.split(',');
                    $('#wk-zcv-cookie'+sellerId+' li').remove();
                    $('#wk-zcv-cookie'+sellerId).css('display','block');
                    $.each(unique(zip), function (i, value) {
                        if (value) {
                            uniquezip = uniquezip+','+value;
                        }
                    });
                    var zipc = uniquezip.split(',');
                    var l = zipc.length;
                    for (var i=0; i< 6; i++) {
                        if (i == 0) {
                            $('#wk-zcv-cookie'+sellerId).append('<li seller-id="'+sellerId+'" class="wk-zcv-history">'+$t("Recent")+'</li>');
                        }
                        if (zipc[i]) {
                            $('#wk-zcv-cookie'+sellerId).append('<li seller-id="'+sellerId+'" title="'+zipc[i]+'" class="wk-zcv-'+i+'">'+zipc[i]+'</li>');
                        }
                    }
                }

                function unique(array)
                {
                    return array.filter(function (el,index,arr) {
                        return index == arr.indexOf(el);
                    });
                }

                $('[class^=wk-zcv-zipform]').click(function () {
                    var sellerId = $(this).attr('seller-data-id');
                    $('.wk-zcv-zipcookie'+sellerId).css('display','block');
                });

                $("body").click(function (e) {
                    if (!(e.target.className.match(/^wk-zcv-.*$/) || e.target.id.match(/^wk-zcv-.*$/) || e.target.className.match('wk-zcv-zipcookie'))) {
                        $("[class^=wk-zcv-zipcookie]").hide();
                    }
                });

                $(document).on('click', '[id^=wk-zcv-addr] li', function (event) {
                    count++;
                    var sellerId =  $(this).attr('seller-id');
                    var productId =  $("[seller-data-id="+sellerId+"]").attr("data-id");
                    zipcode = $(this).attr('title');
                    if (zipcode != prevzip || prevseller != sellerId) {
                        if (zipcode) {
                            $('.wk-zcv-zipcookie'+sellerId).hide();
                            $('.wk-zcv-ziperror'+sellerId).css('display','none');
                            $('.wk-zcv-zipsuccess'+sellerId).css('display','none');
                            $('.wk-zcv-zipform'+sellerId).attr('value',zipcode);
                            $('.wk-zcv-loader'+sellerId).css('display','block');
                            callAjax(zipcode, productId, sellerId);
                            prevzip = zipcode;
                            prevseller = sellerId;
                        }
                    } else {
                        if (zipcode) {
                            $('[class^=wk-zcv-zipcookie]').hide();
                        }
                    }
                });

                $(document).on('click', '[id^=wk-zcv-cookie] li', function (event) {
                    count++;
                    var sellerId =  $(this).attr('seller-id');
                    var productId =  $("[seller-data-id="+sellerId+"]").attr("data-id");
                    var zipcode = $(this).attr('title');
                    if (zipcode != prevzip || prevseller != sellerId) {
                        if (zipcode) {
                            $('.wk-zcv-zipcookie'+sellerId).hide();
                            $('.wk-zcv-zipform'+sellerId).attr('value',zipcode);
                            $('.wk-zcv-ziperror'+sellerId).css('display','none');
                            $('.wk-zcv-zipsuccess'+sellerId).css('display','none');
                            $('.wk-zcv-loader'+sellerId).css('display','block');
                            callAjax(zipcode, productId, sellerId);
                            prevzip = zipcode;
                            prevseller = sellerId;
                        }
                    } else {
                        if (zipcode) {
                            $('[class^=wk-zcv-zipcookie]').hide();
                        }
                    }
                });

                $(document).on('click', '[id^=wk-zcv-login] li', function (event) {
                    $('[class^=wk-zcv-zipcookie]').show();
                });
                
                if (count == 0) {
                    $("input[name='zipcode']").each(function () {
                        var sellerId = $(this).attr('seller-data-id');
                        callAjax('','', sellerId);
                    })
                    $('[class^=wk-zcv-zipcookie]').css('display','none');
                    count++;
                }

                $('.captcha-reload').click();
            })
        }
    });
    return $.fahim.viewonproduct;
});  