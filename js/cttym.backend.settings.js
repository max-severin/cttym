/**
 * cttym.backend.settings.js
 * Module cttymBackendSettings
 */

/*global $, cttymBackendSettings */

var cttymBackendSettings = (function () { "use strict";
    //---------------- BEGIN MODULE SCOPE VARIABLES ---------------
    var
        getProductBlock, keyupTimeout, ajaxSendQuery, onSearchInputKeyup, searchAjaxStatus, onResultBlockScroll, onNonResultBlockClick, onChoosingProduct, onFormSubmitVerify, initModule;
    //----------------- END MODULE SCOPE VARIABLES ----------------

    //--------------------- BEGIN DOM METHODS ---------------------
    getProductBlock = function (product) {
        var                                 
            wrapperBlock, wrapperLeft, wrapperRight, productImg, productName, productId, productBrands, productCategory, productPrice, productLink;

        wrapperBlock    = $('<div/>').addClass('yoss-result-wrapper');
        wrapperLeft     = $('<div/>').addClass('yoss-result-left');
        wrapperRight    = $('<div/>').addClass('yoss-result-right');

        productImg      = $('<a/>').attr('href', '#').addClass('product-image').html(product.image);
        productName     = $('<a/>').attr('href', '#').addClass('product-name').html(product.name);
        productId       = $('<input/>').attr('type', 'hidden').addClass('searched-product-id-input').val(product.id);
        productBrands   = $('<div/>').addClass('product-brand');
        productCategory = $('<div/>').addClass('product-category').html(product.category);
        productPrice    = $('<div/>').addClass('product-price').html(product.price);

        if (product.brands.length > 0) {
            for(var b in product.brands) {
                productBrands = productBrands.append(product.brands[b].brand);
            }
        }

        wrapperLeft.append(productImg, productName, productId, productBrands, productCategory);
        wrapperRight.append(productPrice);
        wrapperBlock.append(wrapperLeft, wrapperRight);

        return wrapperBlock
    };
    //--------------------- END DOM METHODS -----------------------

    //------------------- BEGIN EVENT HANDLERS --------------------
    ajaxSendQuery = function (t, resultBlock) {
        $.ajax({
            type: 'POST',
            url: '?plugin=cttym&action=getproducts',
            data: 'query='+t.val()+'&page=1',
            success: function (result) {

                resultBlock.removeClass('loading');

                if (result.status === 'ok' && result.data !== false) {

                    if (result.data.products.length > 0) {

                        if (result.data.next_page !== false) {
                            var nextPage = $('<input/>').attr('type', 'hidden').attr('id', 'next_page').val(result.data.next_page);
                        } else {
                            var nextPage = $('<input/>').attr('type', 'hidden').attr('id', 'next_page').val('0');
                        }
                        resultBlock.append(nextPage);

                        for(var key in result.data.products) {
                            var productBlock = getProductBlock(result.data.products[key]);
                            resultBlock.append(productBlock);
                        }


                    } else {

                        resultBlock.addClass('no-products').html('{_wp("Sorry, but nothing was found, try to change your query")}');

                    }

                } else {

                    resultBlock.addClass('yoss-error').html('{_wp("Sorry, error accured")}');

                }
            }
        }, 'json');
    };

    onSearchInputKeyup = function (event) {
        var t = $(this);
        searchAjaxStatus = false;

        if ( t.val().length >= 2 ) {

            var inputOffset = t.offset();
            var inputHeight = t.outerHeight() - 1;
            var inputParentWidth = t.parent().outerWidth();

            var cttymItemId = t.closest('.cttym-product').attr('cttym-item-id');

            var resultBlock = $('<div/>').attr('cttym-item-id', cttymItemId).addClass('yoss-result loading').css({
                'left':       inputOffset.left + 'px',
                'max-height': '400',
                'top':        (inputOffset.top + inputHeight) + 'px',
                'width':      t.outerWidth() + 'px'
            });

            if ($('.yoss-result').length > 0) {
                $('.yoss-result').remove();
            } 

            t.addClass('active');
            $('body').prepend(resultBlock);

            if (keyupTimeout) {
                clearTimeout(keyupTimeout);
                keyupTimeout = null;
            }

            keyupTimeout = setTimeout (function() { ajaxSendQuery(t, resultBlock); }, 700);         
            
            $('.yoss-result').scroll(onResultBlockScroll);

        } else {

            t.removeClass('active');
            $('.yoss-result').remove();
            return false;

        }   
    };

    onResultBlockScroll = function (event) {
        var resultBlock = $(this);

        if(resultBlock.scrollTop() + resultBlock.innerHeight() >= this.scrollHeight) {
            if (!searchAjaxStatus) {
                searchAjaxStatus = true;

                var query = $('.cttym-add-product-name-input').val();
                var nextPage = resultBlock.find('#next_page').val();
                var loadingBlock = $('<div/>').addClass('yoss-result-wrapper loading');
                var lastEl = resultBlock.find('.yoss-result-wrapper:last-child');

                if (query.length > 0 && nextPage > 0 ) {
                    lastEl.after(loadingBlock);

                    $.ajax({                        
                        type: 'POST',
                        url: '?plugin=cttym&action=getproducts',
                        data: 'query='+query+'&page='+nextPage,
                        success: function (result) {
                            searchAjaxStatus = false;

                            $('.yoss-result-wrapper.loading').remove();

                            if (result.status === 'ok' && result.data.products.length > 0) {                            

                                if (result.data.next_page !== false) {
                                    resultBlock.find('#next_page').val(result.data.next_page);
                                } else {
                                    resultBlock.find('#next_page').val('0');
                                }

                                for(var key in result.data.products) {
                                    var productBlock = getProductBlock(result.data.products[key]);

                                    lastEl.after(productBlock);
                                }   

                            }
                        }
                    }, 'json');
                }
            }
        }
    };
    
    onNonResultBlockClick = function (event) {
        var div = $(event.data);

        if (!div.is(event.target) && div.has(event.target).length === 0) {
            div.remove();
        }
    };

    onChoosingProduct = function (event) {
        var t = $(this);
        var ctyymItemId = t.closest('.yoss-result').attr('cttym-item-id');
        var newProductName = t.closest('.yoss-result').find('.product-name').text();
        var newProductId = t.closest('.yoss-result').find('.searched-product-id-input').val();
        var productIdHiddenInput = $('input[name="product_id"][value="' + ctyymItemId + '"]');

        $('.cttym-product[cttym-item-id="' + ctyymItemId + '"]').find('input[name="product_id"]').val(newProductId).trigger('change');
        $('.cttym-product[cttym-item-id="' + ctyymItemId + '"]').find('input[name="product_name"]').val(newProductName).trigger('change');

        t.closest('.yoss-result').remove();

        return false;
    };

    onFormSubmitVerify = function (event) {
        if (event.type == 'submit') {
            var f = $(this);
        } else if (event.type == 'change') {
            var f = $(this).closest('form');
        }        
        var allFilled = true;

        f.find('input[type="submit"]').removeAttr('disabled');

        f.find('input[type="text"], input[type="hidden"], textarea').each(function () {
            $(this).css('border-color', '');

            if ($(this).val() == '') {
                f.find('input[type="submit"]').attr('disabled', 'disabled');
                $(this).css('border-color', 'red');

                allFilled = false;
            }
        });

        if (!allFilled) {
            return false;
        }
    };
    //------------------- END EVENT HANDLERS ----------------------

    //------------------- BEGIN PUBLIC METHODS --------------------
    initModule = function () {
        $(document).on('keyup', '.cttym-add-product-name-input', onSearchInputKeyup);

        $(document).on('click', '.yoss-result-wrapper', onChoosingProduct);

        $(document).mouseup('.yoss-result', onNonResultBlockClick);

        $(document).on('submit', '.cttym-product', { 'type': 'submit' }, onFormSubmitVerify);
        $(document).on('change', '.cttym-product input[type="text"], textarea', { 'type': 'keyup' }, onFormSubmitVerify);
    };

    return {
        initModule: initModule
    };
    //------------------- END PUBLIC METHODS ----------------------
}());