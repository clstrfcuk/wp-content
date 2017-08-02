(function ( $ ) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $ ( function () {

        /* Convert All Page */

        var $convert_all_link = $ ( '#convert-all-link' );
        var convert_all_link_url = $convert_all_link.attr ( 'href' );
        if ( $convert_all_link && ($convert_all_link != undefined) && convert_all_link_url && (convert_all_link_url != undefined) ) {
            setTimeout ( function () {
                document.location = convert_all_link_url;
            }, 1000 );
        }


        /* Settings Page */
        $ ( '.wppp-settings-page .no-account a.expand' ).on ( 'click', function ( e ) {
            e.preventDefault ();
            $ ( '.wppp-settings-page .no-account a.expand' ).toggleClass ( 'hidden' );
            $ ( '.wppp-settings-page .no-account .collapsible' ).toggleClass ( 'hidden' );
        } );


    } );

    /*setings count compress imgs*/
    $ ( function () {

        var numberImgs = 500;
        // var totalCheckbox = $ ( '.imageSizesPiexpie input:checkbox' ).length;
        var checkedCheckbox = $ ( '.imageSizesPiexpie input:checked' ).length;
        var number;

        if ( checkedCheckbox == 1 ) {
            number = 500;
            $ ( '.numberCompressImgs' ).text ( number );

        } else if ( checkedCheckbox == 0 ) {
            number = 'N';
            $ ( '.numberCompressImgs' ).text ( number );

        } else {
            number = Math.floor ( numberImgs / checkedCheckbox );
            $ ( '.numberCompressImgs' ).text ( number );
        }

        $ ( '.imageSizesPiexpie input' ).click ( function () {
            checkedCheckbox = $ ( '.imageSizesPiexpie input:checked' ).length;
            if ( checkedCheckbox == 1 ) {
                number = 500;
                $ ( '.numberCompressImgs' ).text ( number );
            } else if ( checkedCheckbox == 0 ) {
                number = 'N';
                $ ( '.numberCompressImgs' ).text ( number );

            } else {
                number = Math.floor ( numberImgs / checkedCheckbox );
                $ ( '.numberCompressImgs' ).text ( number );
            }
        } );
    } );

    $ ( document ).ready ( function () {

        function goAJAX ( totalUnpro ) {
            jQuery.ajax ( {
                type : 'post',
                data : {
                    admin : admin,
                    total_todo : totalUnpro,
                    action : "convert"
                },
                url : "../wp-content/plugins/wp-pixpie/utils/wppp_convert_all_answer.php",
                beforeSend : function () {
                },
                success : function ( response ) {
                    total_todo -= 1;

                    if ( response ) {

                        console.log ( response );

                        var resp = JSON.parse ( response );
                        ajaxContinue = resp[ "2" ];
                    } else {
                        ajaxContinue = false;
                        location.reload ();
                    }

                    // console.log ( 'ajaxContinue : ' + ajaxContinue );

                    document.getElementById ( 'unprocessedNumber' ).textContent = total_todo;
                    var percent = (((total.textContent - total_todo) / total.textContent) * 100).toFixed ( 1 );

                    document.getElementById ( 'wppp_progress-size' ).style.width = percent + '%';
                    document.getElementById ( 'wppp_percentage' ).textContent = percent + '%';
                    document.getElementById ( 'wppp_optimized-current' ).textContent = total.textContent - total_todo;

                    if ( total_todo > 0 && ajaxContinue ) {
                        goAJAX ( total_todo );
                    } else {
                        $ ( '.wppp_progress' ).removeClass ( 'wppp_animate_progress' );
                        console.log ( 'fin' );
                    }
                },
                error : function () {
                    console.log ( 'false' );
                    location.reload ();
                }
            } );
        }

        function checkUpdateStatusAvailable () {
            jQuery.ajax ( {
                type : 'post',
                data : {
                    progress : 'progress'
                },
                url : "../wp-content/plugins/wp-pixpie/utils/wppp_payment_check.php",
                beforeSend : function () {
                },
                success : function ( response ) {

                    response = JSON.parse ( response );
                    // console.dir ( response );

                    if ( response[ 0 ] == 'AVAILABLE' ) {
                        if ( response.length == 2 ) {
                            var loaderBox = document.getElementById ( 'ErrorPixpie' );
                            var div = document.createElement ( 'div' );
                            div.className = 'updated';
                            div.innerHTML = response[ 1 ];
                            loaderBox.parentNode.insertBefore ( div, loaderBox );
                            loaderBox.parentNode.removeChild ( loaderBox );
                        }
                    } else {
                        setTimeout ( checkUpdateStatusAvailable, 5000 );
                    }

                },
                error : function () {
                    console.log ( 'false' );
                }
            } );
        }

        function checkActiveStatusAvailable () {
            jQuery.ajax ( {
                type : 'post',
                data : {},
                url : "../wp-content/plugins/wp-pixpie/utils/wppp_payment_check.php",
                beforeSend : function () {
                },
                success : function ( response ) {

                    response = JSON.parse ( response );
                    console.dir ( response );

                    if ( response[ 0 ] == 'AVAILABLE' ) {
                        if ( response.length == 2 ) {
                            var loaderBox = document.getElementById ( 'ErrorPixpie' );
                            var div = document.createElement ( 'div' );
                            div.className = 'updated';
                            div.innerHTML = response[ 1 ];
                            loaderBox.parentNode.insertBefore ( div, loaderBox );
                            loaderBox.parentNode.removeChild ( loaderBox );
                        }
                    } else {
                        setTimeout ( checkActiveStatusAvailable, 5000 );
                    }

                },
                error : function () {
                    console.log ( 'false' );
                }
            } );
        }

        function checkRenew () {
            jQuery.ajax ( {
                type : 'post',
                data : {},
                url : "../wp-content/plugins/wp-pixpie/utils/wppp_renew.php",
                beforeSend : function () {
                },
                success : function ( response ) {

                    response = JSON.parse ( response );
                    // console.dir ( response );

                    var loaderBox = document.getElementById ( 'ErrorPixpie' );
                    var div = document.createElement ( 'div' );
                    div.className = 'updated';
                    div.innerHTML = response[ 1 ];
                    loaderBox.parentNode.insertBefore ( div, loaderBox );
                    loaderBox.parentNode.removeChild ( loaderBox );

                },
                error : function () {
                    console.log ( 'false' );
                }
            } );
        }

        /*change progress bar convertAll*/
        if ( document.getElementById ( 'wppp_progress-size' ) ) {
            var total = document.getElementById ( 'wppp_optimized-total' ),
                current = document.getElementById ( 'wppp_optimized-current' ),
                percent = ((current.textContent / total.textContent) * 100).toFixed ( 1 ),
                percentage = document.getElementById ( 'wppp_percentage' );

            percentage.textContent = percent + '%';
            document.getElementById ( 'wppp_progress-size' ).style.width = percent + '%';


            var admin = $ ( '#admin' ).text ();
            var total_todo = $ ( '#total_todo' ).text ();

            var ajaxContinue = true;

            // AJAX for load imgs convertAll
            $ ( '#convertAllImgages' ).click ( function ( e ) {
                e.preventDefault ();

                var I = $ ( this );

                goAJAX ( total_todo );
                setTimeout ( function () {
                    I.remove ();
                }, 300 );
                I.fadeOut ( 200 );

                return false;
            } );
        }

        if ( document.getElementById ( 'pixpieUpgradeLink' ) ) {

            var res200 = '';

            $ ( '#pixpieUpgradeLink' ).click ( function ( e ) {

                e.preventDefault ();
                $ ( '#wpppAgree' ).fadeIn ();

                $ ( '.wpppYes' ).click ( function () {
                    var loaderBox = document.getElementById ( 'ErrorPixpie' );
                    loaderBox.innerHTML = '';
                    loaderBox.className = 'wppp_loader';
                    $ ( '#wpppAgree' ).fadeOut ();

                    jQuery.ajax ( {
                        type : 'post',
                        data : {
                            progress : 'progress'
                        },
                        url : "../wp-content/plugins/wp-pixpie/utils/wppp_update_tariff_plan.php",
                        beforeSend : function () {
                        },
                        success : function ( response ) {
                            if ( response ) {
                                response = JSON.parse ( response );
                                // console.dir ( 'response : ' + response );
                                if ( response[ 0 ] == 200 ) {
                                    checkUpdateStatusAvailable ();
                                } else {
                                    var p = $ ( '#ErrorPixpie' );
                                    p.before ( response[ 1 ] );
                                    p.remove ();
                                }
                            }
                        },
                        error : function () {
                            console.log ( 'false' );
                            // location.reload ();
                        }
                    } );
                } );

                $ ( '.wpppNo' ).click ( function () {
                    $ ( '#wpppAgree' ).fadeOut ();
                } );


            } );
        }

        if ( document.getElementById ( 'pixpieSingleSignOnLink' ) ) {

            $ ( '#pixpieSingleSignOnLink' ).click ( function ( e ) {
                e.preventDefault ();
                var href = $ ( this ).attr ( 'href' );
                console.log ( href );
                $ ( '#wpppAgree' ).fadeIn ();

                $ ( '.wpppYes' ).click ( function () {
                    var loaderBox = document.getElementById ( 'ErrorPixpie' );
                    loaderBox.innerHTML = '';
                    loaderBox.className = 'wppp_loader';
                    $ ( '#wpppAgree' ).fadeOut ();

                    window.open ( href );

                    checkActiveStatusAvailable ();
                } );

                $ ( '.wpppNo' ).click ( function () {
                    $ ( '#wpppAgree' ).fadeOut ();
                } );


            } );
        }

        if ( document.getElementById ( 'renewSubscription' ) ) {

            $ ( '#renewSubscription' ).click ( function () {
                checkRenew ();
            } );

        }

        if ( document.getElementById ( 'wpppHideMsg' ) ) {
            $ ( '#wpppHideMsg' ).click ( function ( e ) {
                e.preventDefault ();

                jQuery.ajax ( {
                    type : 'post',
                    data : {},
                    url : "../wp-content/plugins/wp-pixpie/utils/wppp_hide_media_msg.php",
                    beforeSend : function () {
                        $ ( '.warning' ).fadeOut ( 200 );
                    },
                    success : function ( response ) {
                    },
                    error : function () {
                        console.log ( 'false' );
                    }
                } );

            } );
        }

    } );

    if ( document.querySelector ( '.deactivate' ) ) {
        var deactivations = document.querySelectorAll ( '.deactivate a' );
        var what;
        for ( var i = 0; i < deactivations.length; i ++ ) {
            if ( deactivations[ i ].getAttribute ( 'aria-label' ) == 'Deactivate WP Pixpie Plugin' ) {
                what = deactivations[ i ];
                deactivations[ i ].onclick = function ( e ) {
                    e.preventDefault ();
                    var form = document.createElement ( 'form' );
                    form.setAttribute ( 'id', 'wpppFeedback' );
                    form.innerHTML = '<h2>Leave your feedback about Pixpie plugin</h2> <textarea name="feedback" id="wpppFeedbackMassages"></textarea> <div class="wpppButtonWrapper"><button class="wpppSubmit">Submit</button> <button class="wpppCancel" >Cancel</button></div>';
                    document.body.appendChild ( form );

                    document.querySelector ( '.wpppCancel' ).onclick = function ( e ) {
                        e.preventDefault ();
                        window.location.replace ( what.getAttribute ( 'href' ) );
                    };

                    document.querySelector ( '.wpppSubmit' ).onclick = function ( e ) {
                        e.preventDefault ();

                        setTimeout ( function () {
                            form.innerHTML = '<h3>Thanks!</h3>';
                        }, 1000 );

                        jQuery.ajax ( {
                            type : 'post',
                            data : {
                                description : document.querySelector ( '#wpppFeedbackMassages' ).value
                            },
                            url : "../wp-content/plugins/wp-pixpie/utils/wppp_feedback.php",
                            beforeSend : function () {
                            },
                            success : function ( response ) {
                                // console.log ( response );
                                window.location.replace ( what.getAttribute ( 'href' ) );
                            },
                            error : function () {
                                console.log ( 'false' );
                                // location.reload ();
                            }
                        } );
                    }
                };

            }

        }
    }


}) ( jQuery );
