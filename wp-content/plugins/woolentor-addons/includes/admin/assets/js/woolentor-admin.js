;(function($){
"use strict";

    // Tab Menu
    function woolentor_admin_tabs( $tabmenus, $tabpane ){
        $tabmenus.on('click', 'a', function(e){
            e.preventDefault();
            var $this = $(this),
                $target = $this.attr('href');
            $this.addClass('wlactive').parent().addClass('wlactive').siblings().removeClass('wlactive').children('a').removeClass('wlactive');
            $( $tabpane + $target ).addClass('wlactive').siblings().removeClass('wlactive');
        });
    }

    // Navigation Tabs
    $('.woolentor-admin-main-nav').on('click', '.woolentor-admin-main-nav-btn', function(e) {
        e.preventDefault()
        const $this = $(this),
            $siblingsBtn = $this.closest('li').siblings().find('.woolentor-admin-main-nav-btn'),
            $target = $this.attr('href')
        localStorage.setItem("wlActiveTab", $target);
        if(!$this.hasClass('wlactive')) {
            $this.addClass('wlactive')
            $siblingsBtn.removeClass('wlactive')
            $($target).addClass('wlactive').show().siblings().removeClass('wlactive').hide()
        }
    })
    if (localStorage.wlActiveTab !== undefined && localStorage.wlActiveTab !== null ) {
        const $wlActiveTab = localStorage.getItem('wlActiveTab')
        $('.woolentor-admin-main-nav-btn').each(function() {
            const $this = $(this),
                $siblingsBtn = $this.closest('li').siblings().find('.woolentor-admin-main-nav-btn')
            if($this.attr('href') === $wlActiveTab) {
                $this.addClass('wlactive')
                $siblingsBtn.removeClass('wlactive')
            }
        })
        $($wlActiveTab).addClass('wlactive').show().siblings().removeClass('wlactive').hide()
    } else {
        const $firstTab = $('.woolentor-admin-main-nav-btn')[7],
            $target = $firstTab.hash
        $firstTab.classList.add('wlactive')
        $($target).addClass('wlactive').show().siblings().removeClass('wlactive').hide()
    }

    /* Number Input */
    $('.woolentor-admin-number-btn').on('click', function(e){
        e.preventDefault()
        const $this = $(this),
            $input = $this.parent('.woolentor-admin-number').find('input[type="number"]')[0]
        if($this.hasClass('increase')) {
            $input.value = Number($input.value) + 1
        } else if($this.hasClass('decrease') && Number($input.value) > 1) {
            $input.value = Number($input.value) - 1
        }
    });

    /* Pro Popup */
    /* Open */
    $('[data-woolentor-pro="disabled"]').on('click', function(e){
        e.preventDefault()
        const $popup = $('#woolentor-admin-pro-popup')
        $popup.addClass('open')
    });
    /* Close */
    $('.woolentor-admin-popup-close').on('click', function(){
        const $this = $(this),
            $popup = $this.closest('.woolentor-admin-popup')
        $popup.removeClass('open')
    });
    /* Close on outside clicl */
    $(document).on('click', function(e){
        if(e.target.classList.contains('woolentor-admin-popup')) {
            e.target.classList.remove('open')
        }
    });
    
    /* Switch Enable/Disable Function */
    $('[data-switch-toggle]').on('click', function(e){
        e.preventDefault();

        const $this = $(this),
        $type = $this.data('switch-toggle'),
        $target = $this.data('switch-target'),
        $switches = $(`[data-switch-id="${$target}"`)

        $switches.each(function(){
            const $switch = $(this)
            if($switch.data('woolentor-pro') !== 'disabled') {
                const $input = $switch.find('input[type="checkbox"');
                var actionBtn = $switch.closest('.woolentor-admin-switch-block-actions').find('.woolentor-admin-switch-block-setting');
                if( $type === 'enable' && $input.is(":visible") ) {
                    $input[0].setAttribute("checked", "checked");
                    $input[0].checked = true;
                    if( actionBtn.hasClass('woolentor-visibility-none') ){
                        actionBtn.removeClass('woolentor-visibility-none');
                    }
                }
                if( $type === 'disable' && $input.is(":visible") ) {
                    $input[0].removeAttribute("checked");
                    $input[0].checked = false;
                    actionBtn.addClass('woolentor-visibility-none');
                }

            }
        });

    });

    /* Select 2 */
    $('.woolentor-admin-select select[multiple="multiple"]').each(function(){
        const $this = $(this),
            $parent = $this.parent();
        $this.select2({
            dropdownParent: $parent,
            placeholder: "Select template"
        });
    })

    /**
     * Admin Module additional setting button
     */
    $('.woolentor-admin-switch label').on('click',function(e){
        var actionBtn = $(this).closest('.woolentor-admin-switch-block-actions').find('.woolentor-admin-switch-block-setting');
        if( actionBtn.hasClass('woolentor-visibility-none') ){
            actionBtn.removeClass('woolentor-visibility-none');
        }else{
            actionBtn.addClass('woolentor-visibility-none');
        }
    });

    // Option data save
    $('.woolentor-admin-btn-save').on('click',function(event){
        event.preventDefault();

        var $option_form = $(this).closest('.woolentor-admin-main-tab-pane').find('form.woolentor-dashboard'),
            $savebtn     = $(this),
            $section     = $option_form.data('section'),
            $field_keys  = $option_form.data('fields');

        $.ajax( {
            url: WOOLENTOR_ADMIN.ajaxurl,
            type: 'POST',
            data: {
                nonce   : WOOLENTOR_ADMIN.nonce,
                section : $section,
                fileds  : $field_keys,
                action  : 'woolentor_save_opt_data',
                data    : $option_form.serializeJSON()
            },
            beforeSend: function(){
                $savebtn.text( WOOLENTOR_ADMIN.message.loading ).addClass('updating-message');
            },
            success: function( response ) {
                $savebtn.removeClass('updating-message').addClass('disabled').attr('disabled', true).text(WOOLENTOR_ADMIN.message.success);
            },
            complete: function( response ) {
                $savebtn.removeClass('updating-message').addClass('disabled').attr('disabled', true).text(WOOLENTOR_ADMIN.message.success);
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }

        });

    });

    // Save Button Enable
    $('.woolentor-admin-main-tab-pane .woolentor-dashboard').on( 'click', 'input,select,.woolentor-admin-number-btn' , function() {
        $(this).closest('.woolentor-admin-main-tab-pane').find('.woolentor-admin-btn-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
    });

    $('.woolentor-admin-header-actions .woolentor-admin-btn').on('click', function(){
        $(this).closest('.woolentor-admin-main-tab-pane').find('.woolentor-admin-btn-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
    });

    $('.woolentor-admin-main-tab-pane .woolentor-dashboard').on('change', 'select.woolentor-admin-select', function() {
        $(this).closest('.woolentor-admin-main-tab-pane').find('.woolentor-admin-btn-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
    });

    // Module additional settings
    $('.woolentor-admin-switch-block-setting').on('click',function(event){
        event.preventDefault();

        var $this     = $(this),
            $section  = $this.data('section'),
            $fields   = $this.data('fields'),
            content = null,
            modulewrapper = wp.template( 'woolentormodule' );

        $.ajax( {
            url: WOOLENTOR_ADMIN.ajaxurl,
            type: 'POST',
            data: {
                nonce   : WOOLENTOR_ADMIN.nonce,
                section : $section,
                fileds  : $fields,
                action  : 'woolentor_module_data',
                subaction : 'get_data',
            },
            beforeSend: function(){
                $this.addClass('module-setting-loading');
            },
            success: function( response ) {

                content = modulewrapper( {
                    section : $section,
                    fileds  : response.data.fields,
                    content : response.data.content
                } );
                $( 'body' ).append( content );

                woolentor_module_ajax_reactive();
                $this.removeClass('module-setting-loading');
                
            },
            complete: function( response ) {
                $this.removeClass('module-setting-loading');
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }

        });


    });

    // PopUp reactive JS
    function woolentor_module_ajax_reactive(){

        // Select 2 Multiple selection
        $('.woolentor-module-setting-popup .woolentor-admin-select select[multiple="multiple"]').each(function(){
            const $this = $(this),
                $parent = $this.parent();
            $this.select2({
                dropdownParent: $parent,
                placeholder: "Select template"
            });
        });

        //Initiate Color Picker
        $('.wp-color-picker-field').wpColorPicker({
            change: function (event, ui) {
                $(this).closest('.woolentor-module-setting-popup-content').find('.woolentor-admin-module-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
            },
            clear: function (event) {
                $(this).closest('.woolentor-module-setting-popup-content').find('.woolentor-admin-module-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
            }
        });

        // WPColor Picker Button disable.
        $('div[data-woolentor-pro="disabled"] .wp-picker-container button').each(function(){
            $(this).attr("disabled", true);
        });

        // Icon Picker
        $('.woolentor_icon_picker .regular-text').fontIconPicker({
            source: woolentor_fields.iconset,
            emptyIcon: true,
            hasSearch: true,
            theme: 'fip-bootstrap'
        }).on('change', function() {
            $(this).closest('.woolentor-module-setting-popup-content').find('.woolentor-admin-module-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
        });

        // Module additional setting save
        $('.woolentor-admin-module-save').on('click',function(event){
            event.preventDefault();

            var $option_form = $(this).closest('.woolentor-module-setting-popup-content').find('form.woolentor-module-setting-data'),
                $savebtn     = $(this),
                $section     = $option_form.data('section'),
                $field_keys  = $option_form.data('fields');

            $.ajax( {
                url: WOOLENTOR_ADMIN.ajaxurl,
                type: 'POST',
                data: {
                    nonce   : WOOLENTOR_ADMIN.nonce,
                    section : $section,
                    fileds  : $field_keys,
                    action  : 'woolentor_save_opt_data',
                    data    : $option_form.serializeJSON()
                },
                beforeSend: function(){
                    $savebtn.text( WOOLENTOR_ADMIN.message.loading ).addClass('updating-message');
                },
                success: function( response ) {
                    $savebtn.removeClass('updating-message').addClass('disabled').attr('disabled', true).text(WOOLENTOR_ADMIN.message.success);
                },
                complete: function( response ) {
                    $savebtn.removeClass('updating-message').addClass('disabled').attr('disabled', true).text(WOOLENTOR_ADMIN.message.success);
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
    
            });

        });

        // Save button active
        $('.woolentor-module-setting-popup-content .woolentor-module-setting-data').on( 'click', 'input,select,.woolentor-admin-number-btn' , function() {
            $(this).closest('.woolentor-module-setting-popup-content').find('.woolentor-admin-module-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
        });

        $('.woolentor-module-setting-popup-content .woolentor-module-setting-data').on('change', 'select', function() {
            $(this).closest('.woolentor-module-setting-popup-content').find('.woolentor-admin-module-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
        });

        /* Close PopUp */
        $('.woolentor-admin-popup-close').on('click', function(){
            const $this = $(this),
                $popup = $this.closest('.woolentor-admin-popup')
            $popup.removeClass('open')
        });

    }

    // Extension Tabs
    woolentor_admin_tabs( $(".woolentor-admin-tabs"), '.woolentor-admin-tab-pane' );

    // Check Save data wise
    WooLentorConditionField( WOOLENTOR_ADMIN.option_data['contenttype'], 'fakes', '.notification_fake' );
    WooLentorConditionField( WOOLENTOR_ADMIN.option_data['contenttype'], 'actual', '.notification_real' );
    WooLentorConditionField( WOOLENTOR_ADMIN.option_data['side_mini_cart'], 'on', '.side_mini_cart_field' );
    WooLentorConditionField( WOOLENTOR_ADMIN.option_data['enablecustomlayout'], 'on', '.depend_enable_custom_layout' );
    WooLentorConditionField( WOOLENTOR_ADMIN.option_data['enablerenamelabel'], 'on', '.depend_enable_rename_label' );

    // After On change
    WooLentorOnChangeField('.notification_content_type .radio', 'radio', '.notification_fake', 'fakes' );
    WooLentorOnChangeField('.notification_content_type .radio', 'radio', '.notification_real', 'actual' );
    WooLentorOnChangeField('.side_mini_cart .checkbox', 'radio', '.side_mini_cart_field', 'on' );
    WooLentorOnChangeField('.enablecustomlayout .checkbox', 'radio', '.depend_enable_custom_layout', 'on' );
    WooLentorOnChangeField('.enablerenamelabel .checkbox', 'radio', '.depend_enable_rename_label', 'on' );

    function WooLentorOnChangeField( field, type = 'select', selector, condition_value ){
        $(field).on('change',function(){
            var change_value = '';
            if( type === 'radio' ){
                if( $(this).is(":checked") ){
                    change_value = $(this).val();
                }
            }else{
                change_value = $(this).val();
            }
            WooLentorConditionField( change_value, condition_value, selector );
        });
    }

    // Hide || Show
    function WooLentorConditionField( value, condition_value, selector ){
        if( value === condition_value ){
            $(selector).show();
        }else{
            $(selector).hide();
        }
    }
        
})(jQuery);