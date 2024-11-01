jQuery(document).ready(function ($) {
    "use strict";
	var screen = $(window).width();

	var screenXs = 480,
		screenSm = 768,
		screenMd = 992,
		screenLg = 1200;

	$(window).on('resize', function() {
		screen = $(window).width();
	});

	var scrollWidth = window.innerWidth - document.body.clientWidth;

	$(document).on('shown.bs.modal', '.modal', function () {
        if($('body').is('.modal-open')) {
            $('body').css('cssText', 'padding-right: ' + scrollWidth + 'px !important;');
        }
    });

	var Selector = {
		wrapper       : '#ttlc-container',
		contentWrapper: '.ttlc-wrapper',
		editorID : 'ttlc-ckeditor',
	}
	
	var TTLC = {
		attachments : [],
	}
	
	history.replaceState({url: window.location.href}, null);

	function initCkeditor() {
		if ( $( '#' + Selector.editorID ).length ) {
			CKEDITOR.replace( Selector.editorID );
		}
		
	}
	
	function reloadWrapper( url = false, pushState = true ){
		if( url === false ) {
			var url = window.location.href;
		}
		$(Selector.wrapper).addClass('ttlc__disabled');
		$( Selector.wrapper ).load( url.split('#')[0] + ' ' + Selector.contentWrapper, function(){
			if ( pushState ) {
				history.pushState({url: url}, null, url);			
			}
			$(Selector.wrapper).removeClass('ttlc__disabled');
			initCkeditor();
			wp.heartbeat.connectNow();
		} );

	}
	
	function csir(btn){
		$(Selector.wrapper).addClass('ttlc__disabled');
		var form = $('#ttlc-add-ticket');
		var data = form.serializeArray();
		var csirID = btn.siblings('[name="csir-id"]').val();
		var type = btn.data('type');
		data.push({
			name : 'action',
			value : 'ttlc/add/ticket'
		},
		{
			name : 'type',
			value : type
		},
		{
			name : 'csirID',
			value : csirID
		});
		$.post( {
			'url' : ajaxurl,
			'data' : data,
		}).done( function( response ) {
			reloadWrapper();
		}).fail( function(){
			alert('Ajax error');
		});
	}

	function updatePendingCount(selector, value) {
	    var countEl = $(selector);
	    if(!countEl.length) return;

	    if(value){
	        countEl.text(value).removeClass('count-0');
	    } else {
		    countEl.addClass('count-0');
	    }
	}

	function updatePendingCounts(counts){
		$.each(counts, function(index, count){
		    updatePendingCount(count.selector, count.value);
		});
	}
	
	function getCheckboxValue(index, el){
		return {name: el.name, value: el.checked ? 'y' : '' };
	}
	
	function addCheckboxesValues(data, form){
		var checkboxesData = $('input:checkbox', form).map(getCheckboxValue);
		checkboxesData.each(function(index, el){
			if ( ! el.value ) {
				data.push(this);
			}
		});
		return data;
	}
	
	window.onpopstate = function(e){
		if( e.state ) {
			reloadWrapper(e.state.url, false);
		}
	}
	
	initCkeditor();

	$(document).on('click', 'a[href="#"]', function(e) {
		e.preventDefault();
	});

	/* =================== Scroll ==================== */

	$(document).on('click', 'a[data-scroll]', function(e){
		e.preventDefault();
		var target = $(this.getAttribute('href'));
		if( target.length ) {
			$('html, body').stop().animate({
				scrollTop: target.offset().top - 82
			}, 400);
		}
	});

	/* ============== Ticket Row Click =============== */

	$(document).on('click', '.ttlc__tickets-inner table > tbody > tr', function(){
		var url = $(this).children('td:nth-child(2)').children('a').attr('href');
		document.location.href = url;
	});	

	/* ============== Newsletter Row Click =============== */

	$(document).on('click', '.ttlc__newsletters-inner table > tbody > tr', function(e){
		var modal = $($(this).find('.newsletter-modal-link').data('target'));
		if(modal.length){
			modal.modal('show');
		}
	});

	/* ================ Newsletter Read ================= */
	
	$(document).on('shown.bs.modal', '.new-newsletter-modal', function(){
		var modal = $(this);
		setTimeout( function(){
			var id = modal.data('newsletter-id');
			var nonce = modal.data('nonce');
			if(id && nonce){
				$.post( {
					'url' : ajaxurl,
					'data' : { action : 'ttlc/newsletter/read', id : id, _wpnonce : nonce },
				}).done( function( response ) {
					if ( response.status ) {
						modal.removeClass('new-newsletter-modal');
						modal.addClass('read-newsletter-modal');
					}
				});
			}
		}, 2000 );
	});
	
	$(document).on('hide.bs.modal', '.read-newsletter-modal', function() {
		reloadWrapper();
	});

	/* ================ Check Server ================= */

	$(document).on('click', '.ttlc-product-server-check', function(e){
		e.preventDefault();
		var form = $(this).parents('.ttlc-product-server-check-form');
		$(form).trigger('submit');
	});

	$(document).on('submit', '.ttlc-product-server-check-form', function(e){
		e.preventDefault();
		var form = $( this );
		var modal = form.parents('.modal-dialog');
		var data = form.serializeArray();
		var action = 'ttlc/server/check';
		modal.addClass('ttlc__disabled');
		data.push( {
			'name' : 'action',
			'value' : action,
		} );

		$.post( {
			'url' : ajaxurl,
			'data' : data,
		}).done( function( response ) {
			if ( response ) {
				modal.replaceWith( $(response).find('.modal-dialog') );
			}
		});
	});

	$(document).on('change', '.ttlc-product-select', function(e){
		e.preventDefault();
		var productID = $(this).val();
		if(productID){
			var el = $('#' + productID);
			if(el){
				el.removeClass('collapse');
				el.siblings().addClass('collapse');
			}
		}
	});

	/* ================ Product Dynamic Licenses ================= */

	$(document).on('change', '.ttlc-license-select', function(){
		var form = $(this).parents('form');
		var target = $(this).val();
		$('.ttlc-license-fields-' + target, form).removeClass('collapse').find('input, textarea, select').prop('disabled', false);
		$('.ttlc-license-fields-' + target, form).siblings().addClass('collapse').find('input, textarea, select').prop('disabled', true);
	});
	
	$(document).on('click', '.ttlc-license-field-checkbox', function(){
		var target = '#' + $(this).val();
		var disabled = ! $(this).prop('checked');
		$(target).prop('disabled', disabled).parents('.form-group').toggleClass('collapse');
	});

	/* ================ Save Product ================= */
	
	$(document).on('click', '.ttlc-product-save-btn', function(){
		var form = $(this).parents('.modal-content').find('.ttlc-product-fields:not(.collapse)').find('.tab-pane.active form');
		form.trigger('submit');
	});

	$(document).on('submit', '.ttlc-product-settings-form', function(e){
		e.preventDefault();
		var form = $(this);
		var modal = form.parents('.modal');
		var modalDialog = $('.modal-dialog', modal);
		var data = form.serializeArray();
		data = addCheckboxesValues(data, form);
		var action = 'ttlc/product/save';
		modalDialog.addClass('ttlc__disabled');
		data.push( {
			'name' : 'action',
			'value' : action,
		} );

		$.post( {
			'url' : ajaxurl,
			'data' : data,
		}).done( function( response ) {
			if ( response.status ) {
				modal.modal('hide');
				$( modal ).one('hidden.bs.modal', function(){
					reloadWrapper();
				});
			} else {
				form.replaceWith( response.data );
			}
		}).fail( function( response ) {
			alert('Ajax Error');
		}).always( function(){
			modalDialog.removeClass('ttlc__disabled');
		});
	});

	/* ================ Trash Product ================= */
	
	$(document).on('click', '.ttlc-product-trash, .ttlc-product-untrash, .ttlc-newsletter-trash', function(e){
		e.preventDefault();
		var data = $(this).attr('href').split('?')[1];
		$(Selector.wrapper).addClass('ttlc__disabled');
		$.post( {
			'url' : ajaxurl,
			'data' : data,
		} ).done( function( response ) {
			if ( response.status ) {
				reloadWrapper();
			} else {
				alert(response.data);
				$(Selector.wrapper).removeClass('ttlc__disabled');
			}
		}).fail( function(){
			$(Selector.wrapper).removeClass('ttlc__disabled');
			alert('Ajax Error');
		});
	});

	/* ============ Change Password Input ============ */

	$(document).on('click', '.ttlc-password-toggle', function(){
		$(this).toggleClass('active').children('i').toggleClass('fa-eye-slash').toggleClass('fa-eye');
		var input = $('[name=password]', $(this).parents('.form-group'));
		var type = input.attr('type');
		
		if (type === 'password') {
			input.attr('type', 'text');
		} else {
			input.attr('type', 'password');			
		}			
		
	});

	/* ============ Password Reset ============ */

	$(document).on('submit', '.ttlc-product-settings-password-reset', function(e){
		e.preventDefault();
		var form = $(this);
		var step = form.parents('.modal-content');
		var stepID = step.attr('id');
		var nextStep = step.next();
		var data = form.serializeArray();
		var action = 'ttlc/password/reset';
		step.addClass('ttlc__disabled');
		data.push( {
			'name' : 'action',
			'value' : action,
		} );

		$.post( {
			'url' : ajaxurl,
			'data' : data,
		}).done( function( response ) {
			if ( response.status ) {
				var emailLogin = $('[name=email_login]', form).val();
				$('[name=email_login]', nextStep).val( emailLogin );
				step.removeClass('in');
				nextStep.addClass('in');
				$('.has-error', step).removeClass('has-error');
				$('.help-block', step).remove();
				if ( response.data.selector && response.data.value ) {
					$(response.data.selector).val(response.data.value);
				}
			} else {
				if ( response.data ) {
					form.replaceWith( $(response.data).find('#' + stepID + ' form') );
				}
			}
		}).fail( function( response ) {
			alert('Ajax Error');
		}).always( function(){
			step.removeClass('ttlc__disabled');
		});
	});	

	/* ============ Tabs ============ */

	$(document).on('click', '.ttlc-tabs a', function(e){
		e.preventDefault();
		if ( ! $(this).hasClass('disabled') ) {
			$(this).addClass('active').siblings('.active').removeClass('active');
			$($(this).attr('href')).addClass('active in').siblings('.active').removeClass('active in');
		}
	});

	$(document).on('click', '.ttlc-modal-nav', function(){
		var modal = $( $(this).attr('href') );
		if ( modal.length ) {
			modal.addClass('in');
			modal.siblings().removeClass('in');
		}
	});
	
	/* ============ Filter / Pagination ============ */
	
	$(document).on('click', '.ttlc__filter a, .ttlc-pagination a', function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		reloadWrapper( url );
	});

	/* ============ Pagination Ticket ============ */

	$(document).on('click', '.ttlc-pagination-ticket a', function(e){
		e.preventDefault()
		if ( ! $(this).hasClass( 'ttlc-load-more-ticket' ) ) {
			var url = $(this).attr('href');
			reloadWrapper( url );
		}
	});

	/* ============ Load More Ticket ============ */
	
	$(document).on('click', '.ttlc-load-more-ticket', function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		$(Selector.wrapper).addClass('ttlc__disabled');
		$.get({
			url : url
		}).done(function(response){
			if ( response ) {
				var html = $(response);
				$('.ttlc__tickets-responses').append( html.find('.ttlc__tickets-responses').children() );
				$('.ttlc-pagination-ticket').replaceWith( html.find('.ttlc-pagination-ticket') );
			} else {
				alert('Ajax Error');
			}
			$(Selector.wrapper).removeClass('ttlc__disabled');
		});
	});

	/* ============ Sort Responses ============ */
	
	$(document).on('click', '.ttlc__tickets-sort a', function(e){
		e.preventDefault();
		if ( ! $(this).hasClass( 'disabled' ) ) {
			var url = $(this).attr('href');
			reloadWrapper( url );
		}
	});

	/* ============ Add Ticket ============ */

	$(document).on( 'submit', '#ttlc-add-ticket', function (e) {
		e.preventDefault();
		var form = e.target;
		var formData = new FormData( form );
		var action = 'ttlc/add/ticket';
		var attachments = $('#ttlc-attachments');

		$(Selector.wrapper).addClass('ttlc__disabled');

		formData.append( 'action', action );
		formData.delete('attachment' );
		TTLC.attachments.forEach( function( el ) {
			formData.append( 'attachment[]', el, el.name );
		} );
		$.ajax( {
			type : 'post',
			url : ajaxurl,
			data : formData,
			processData : false,
			contentType : false
		} ).done( function( response ) {
			if( response.status ) {
				if ( response.data && response.data.ticket_id ) {

					if ( response.data.ticket_parent ) {
						// Response
						reloadWrapper();
						TTLC.attachments = [];
						
					} else {
						// Ticket
						window.location.replace( window.location.href.split('#')[0] + '&ticket_id=' + response.data.ticket_id );
					}

				}
			} else {
				if ( response.data ) {
					var formUpdate = $( response.data );
					$('#ttlc-attachments', formUpdate).replaceWith( attachments );
					$( form ).replaceWith( formUpdate );

					setTimeout( function(){
						initCkeditor();
					}, 200);
				} else {
					alert( 'error' );
				}
				$(Selector.wrapper).removeClass('ttlc__disabled');
			}
		} ).fail( function( response ) {
			$(Selector.wrapper).removeClass('ttlc__disabled');
			alert( 'error' );
		} );
	} );

	/* ============ Manual Attachment Download / Reload ============ */
	
	$(document).on( 'click', '.ttlc-manual-attachment-download, .ttlc-attachment-reload', function (e) {
		e.preventDefault();
		var el = $( e.target ).closest( 'a' );
		var form = el.find('form');
		var data = form.serializeArray();
		var item = el.closest('li');
		var ext_id = item.data('attachment-external-id');
		var loadingItem = item.siblings('.ttlc-attachment-loading-template').clone().removeClass('hidden ttlc-attachment-loading-template');
		var errorItem = item.siblings('.ttlc-attachment-error-template').clone().removeClass('hidden ttlc-attachment-error-template');
		$('.ttlc-attachment-reload', errorItem).append(form);
		item.replaceWith(loadingItem);
		$('.progress-bar', loadingItem).css('width', '0%');
		$.post({
			url : ajaxurl,
			data : data,
			xhr : function () {
			    var xhr = new window.XMLHttpRequest();
			    //Upload progress
			    xhr.upload.addEventListener('progress', function(evt){
			      if (evt.lengthComputable) {
			        var percentComplete = Math.round(evt.loaded / evt.total * 50);
			        $('.progress-bar', loadingItem).css('width', percentComplete + '%');
			      }
			    }, false);
			    //Download progress
			    xhr.addEventListener('progress', function(evt){
			      if (evt.lengthComputable) {
			        var percentComplete = Math.round(evt.loaded / evt.total * 100);
			        $('.progress-bar', loadingItem).css('width', percentComplete + '%');
			      }
			    }, false);
			    return xhr;
			},
		}).done( function( response ) {
			if ( response.status && response.data ) {
				setTimeout( function(){
					loadingItem.replaceWith(response.data)
					$('[data-attachment-external-id="' + ext_id + '"]').replaceWith(response.data);
				}, 600)
			} else {
				loadingItem.replaceWith(errorItem);
			}
		}).fail(function(response){
			loadingItem.replaceWith(errorItem);
		});

	} );

	/* ============ Attachments on Adding Ticket ============ */
	
	$(document).on( 'change', '#ttlc-ticket-attachment', function (e) {
		var input = e.target;
		for (var i = 0, numFiles = input.files.length; i < numFiles; i++) {
			var newFile = input.files[i];				
			var compare = TTLC.attachments.filter( File => File.name === newFile.name );
			if ( ! compare.length ) {
				TTLC.attachments.push( newFile );
				var fileSize;
				if ( newFile.size >= 1000 ) {
					if ( newFile.size >= 1000000 ) {
						fileSize = Math.ceil( newFile.size / 1000000 ) + ' MB';
					} else {
						fileSize = Math.ceil( newFile.size / 1000) + ' KB';
					}
				} else {
					fileSize = Math.ceil(newFile.size) + ' B';
				}
				var fileName = newFile.name.length > 20 ? newFile.name.substr( 0, 20) + '...' : newFile.name;
				var attachmentBox = $( '.ttlc-attachment-template' ).clone().removeClass( 'hidden ttlc-attachment-template' );
				attachmentBox.find( '.size' ).text( fileSize );
				attachmentBox.find( '.title' ).text( fileName );
				attachmentBox.find( '.ttlc-ticket-attachment-delete' ).data( 'file-name', newFile.name );
				attachmentBox.prependTo( '#ttlc-attachments' );
			}
		}
		input.value = '';
	} );

	/* ============ Attachment Delete on Adding Ticket ============ */
	
	$(document).on( 'click', '.ttlc-ticket-attachment-delete', function (e) {
		e.preventDefault();
		var el = $( e.target ).closest( 'a' );
		var fileName = el.data( 'file-name' );
		el.parents( 'li' ).remove();
		for (var i = 0; i < TTLC.attachments.length; i++) {
			if ( TTLC.attachments[i].name === fileName ) {
				TTLC.attachments.splice( i, 1 );
			}
		}
	} );

	/* ============ Settings ============ */

	$(document).on('submit', '.ttlc-settings', function(e){
		e.preventDefault();
		var form = $(this);
		var data = form.serializeArray();
		data = addCheckboxesValues(data, form);
		$(Selector.wrapper).addClass('ttlc__disabled');
		$.post({
			url : ajaxurl,
			data : data,
		}).done(function(response){
			if( response.data ) {
				form.replaceWith(response.data);
			} else {
				$('.state', form).addClass('text-danger').html('Unknown Error');
			}
		}).fail(function(){
			$('.state', form).addClass('text-danger').html('Unknown Error');
		}).always(function(){
			$(Selector.wrapper).removeClass('ttlc__disabled');
		});
	});
	
	$(document).on('click', '.ttlc-settings [type=submit]', function(e){
		if ($(this).hasClass('disabled')) {
			e.preventDefault();
		}
	});
	
	$(document).on('change', '.ttlc-settings input, .ttlc-settings select', function(){
		var form = $(this).parents('form');
		var state = $('.state', form);
		state.addClass('text-warning').removeClass('text-success text-danger').text(ttlcText.waiting_save);
		$('[type=submit]', form).removeClass('disabled');
	});
		
	/* ============ Ticket Open / Close ============ */

	$(document).on('click', '.ttlc-ticket-edit', function(e){
		e.preventDefault();
		var form = $(this).find('form');
		var data = form.serializeArray();
		$.post( {
			'url' : ajaxurl,
			'data' : data,
		}).done( function( response ) {
			reloadWrapper();
		}).fail( function(){
			alert('Ajax error');
		});
	});

	/* ============ CSIR / CSLR Send / Reject ============ */
	
	$(document).on('click', '.csir-send, .csir-reject', function(e){
		e.preventDefault();
		csir($(this));
	});
	
	/* ============ CSIR Tooltip ============ */
	
	$(document).on('mouseenter', '.ttlc__label-info', function(e){
		$(this).find('.ttlc__label-info-hidden').fadeIn();
	});

	$(document).on('mouseleave', '.ttlc__label-info', function(e){
		$(this).find('.ttlc__label-info-hidden').fadeOut();
	});
	
	/* ============ Pending Events Counters ============ */

    $(document).on('heartbeat-tick', function(e, data){
	    if ( data.ttlc_pending_counts ) {
		    updatePendingCounts(data.ttlc_pending_counts);
	    }
    });
    
    $(document).on('heartbeat-send', function(e, data){
	    var products = [];
	    $('.ttlc__pending-tickets-product-count').each(function(index){
		    var productID = $(this).data('product-id');
		    if(productID){
			    products.push(productID);
		    }
	    });
	    data.ttlc_pending_counts_products = products;
    });
	
});

