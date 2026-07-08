const wpcf7Custom = ($) => {
	$('.wpcf7-submit').toggleClass('btn-primary btn-outline-light btn-block');
}

const likes = ($) => {
	// init load icon
	$('.btn-like>i').toggleClass('far fa-heart fa fa-sync fa-spin')
	setTimeout(() => {
		$('.btn-like>i').toggleClass('far fa-heart fa fa-sync fa-spin');
	}, 200);

	// -----
	const clickLike = (e) => {
		e.preventDefault();
		let $post = $(e.target).closest('.hentry');
		let id = parseInt($post.attr('id').replace('post-', ''));
		if (id) {
			$post.find('.btn-like>i')
				.toggleClass('far fa-heart fa fa-sync fa-spin');
			$(e.target).prop('disabled', true);
			$.ajax(WPURLS.siteurl + '/add-like-post/' + id, { method: 'POST', dataType: 'json' })
				.done((r) => {
					if (r.success) {
						let n = parseInt($post.find('.like-number').text());
						$post.find('.like-number').text(++n);
					}
				})
				.always(() => {
					$post.find('.btn-like>i')
						.toggleClass('far fa-heart fa fa-sync fa-spin');
					$(e.target).prop('disabled', false);
				});
		}
	}
	$('.btn-like').click(clickLike);
}

const starsFields = ($) => {
	$('input.custom-star, label.custom-star').change((e) => {
		$(e.target).closest('.col').find('.custom-star').removeClass('active');
		$(e.target).closest('label').addClass('active');
		$(e.target).closest('.custom-control').prevAll()
			.filter('.custom-control').find('.custom-star').addClass('active');
	});
	$('label.custom-star').closest('.col').each((i, e) => {
		let spans = $(e).find('span.d-none');
		let first = spans.first();
		let last = spans.last();
		let css = {
			position: 'absolute',
			width: 150,
			top: 20,
			left: -30
		};
		$(first).removeClass('d-none')
			.css(css);
		$(last).removeClass('d-none')
			.css(css);
	});
}

const filters = ($) => {
	$('.filters-container').on('change', 'input, select', (e) => {
		$(e.target).closest('form').submit();
	});
}

const menuDropdown = ($) => {
	$('.dropdown-menu a').addClass('text-dark');
	$('.directory-type a').each((i, e) => {
		let href = $(e).attr('href');
		$(e).attr('href', href.replace(/.*\$homeUrl/, WPURLS.siteurl));
	});
}

const owlInit = ($) => {
	let computed = getComputedStyle(document.documentElement);
	let size = {};
	size.xs = parseInt(computed.getPropertyValue('--breakpoint-xs'));
	size.sm = parseInt(computed.getPropertyValue('--breakpoint-sm'));
	size.md = parseInt(computed.getPropertyValue('--breakpoint-md'));
	size.lg = parseInt(computed.getPropertyValue('--breakpoint-lg'));
	size.xl = parseInt(computed.getPropertyValue('--breakpoint-xl'));
	let owlOpts = {
		margin: 20,
		loop: true,
		center: true,
		nav: true,
		center: false,
		responsive: {}
	};
	$(".owl-carousel").each((i, e) => {
		$(e).css('position', 'relative');
		let className = $(e).find(':first-child').attr('class') || '';
		let cols = className.match(/col(\-(sm|md|lg|xl))?\-\d+/g);
		if (!cols) return;
		let owlOptsL = { ...owlOpts };
		owlOptsL.responsive = {};
		cols.forEach(s => {
			sizer = size[s.replace(/col(-(\w+))?-\d+/, '$2')];
			items = 12 / parseInt(s.replace(/col(\-(sm|md|lg|xl))?\-/, ''));
			if (sizer) {
				owlOptsL.responsive[sizer] = { items };
			} else {
				owlOptsL.items = items;
			}
		});
		$(e).find('>.col').removeClass(cols.join(' ') + ' col');
		$(e).removeClass('row').owlCarousel(owlOptsL);
	});
}

const formComplaint = ($) => {
	$('[data-toggle="tooltip"]').tooltip();
	$('input[type="file"]').click((e) => {
		$(e.target).parent().find('label')
			.addClass("selected").text('');
	});
	$('input[type="file"]').on('change', (e) => {
		let fileName = e.target.files[0].name;
		$(e.target).parent().find('label')
			.addClass("selected").text(fileName);
	});
	$('#moreForm_modal').on('show.bs.modal', (e) => {
		$('#moveToModal').appendTo($('#moreForm_modal .modal-body'))
			.wrap('<div class="col col-12" id="wrap"></div>');
		$('#moveToModal').find('.btn-outline-light')
			.toggleClass('btn-outline-light btn-outline-secondary');
	});
	$('#moreForm_modal').on('hide.bs.modal', (e) => {
		let $lastRow = $('[data-target="#moreForm_modal"]').closest('.row');
		$('#moveToModal').insertAfter($lastRow);
		$('#moveToModal').find('.btn-outline-secondary')
			.toggleClass('btn-outline-light btn-outline-secondary');
		$('#wrap').remove();
	});
	// hide email phone and name
	$("[name=anonymous]")
		.change((e) => {
			if (e.target.checked) {
				$('#name, #email, #phone').prop('required', false).each((i, e) => {
					let required = $(e).attr('placeholder');
					required = required.replace(' *', '');
					$(e).attr('placeholder', required);
				});

			} else {
				$('#name, #email, #phone').prop('required', true).each((i, e) => {
					let required = $(e).attr('placeholder');
					required += ' *';
					$(e).attr('placeholder', required);
				});
			}
		});

	// hide elements in form "more"
	$("[name=medications_filled]")
		.change((e) => {
			if (e.target.value == 'No') {
				$(e.target).closest('.col')
					.nextAll().addClass('d-none');
			} else {
				$(e.target).closest('.col')
					.nextAll().removeClass('d-none');
			}
		});
	$("[name=surgical_procedure]")
		.change((e) => {
			if (e.target.value == 'No') {
				$(e.target).closest('.col')
					.nextAll().addClass('d-none');
			} else {
				$(e.target).closest('.col')
					.nextAll().removeClass('d-none');
			}
		});
}

const formComplaintSubmit = ($) => {
	$('#formComplaint input').on('invalid', (e) => {
		$('#moreForm_modal').modal('hide');
	});
	$('input:not([type=checkbox]), select, textarea').on('change valid invalid', (e) => {
		if (e.target.checkValidity()) {
			$(e.target).addClass('is-valid').removeClass('is-invalid');
		} else {
			$(e.target).addClass('is-invalid').removeClass('is-valid');
		}
	});
	$('#formComplaint').on('submit', (e) => {
		e.preventDefault();
		var post_url = $(e.target).attr("action");
		var request_method = $(e.target).attr("method");
		var form_data = new FormData(e.target);
		let $thisBtn = $(e.target).find('button[type=submit]');
		$thisBtn.prop('disabled', true);
		$thisBtn.append('<i class="fa fa-spinner fa-pulse ml-2"></i>');
		$.ajax({
			url: post_url,
			type: request_method,
			data: form_data,
			dataType: 'json',
			contentType: false,
			cache: false,
			processData: false
		})
			.then(function (res) {
				var filter = $.Deferred();
				if (res.success) {
					filter.resolve(res.data);
				} else {
					filter.reject(res);
				}
				return filter.promise();
			})
			.done(function (data) {
				e.target.reset();
				$(e.target).find('input, select').removeClass('is-valid is-invalid');
				$('.custom-file-label').text('');
				$('#response_modal').find('.modal-body').text('Gracias por compartirnos tu experiencia, en 24 horas podrás verla publicada en este sitio');
				$('#response_modal').modal('show');
			})
			.fail((e) => {
				console.error(e)
				$('#response_modal').find('.modal-body').html('<pre>' + JSON.stringify(e, null, 2) + '</pre>');
				$('#response_modal').modal('show');
			})
			.always(function () {
				$thisBtn.prop('disabled', false)
					.find('i').remove();
				$(e.target).find('[type="submit"]').prop('disabled', false);
				setTimeout(() => {
					$('#moreForm_modal').modal('hide');
				}, 200);
			});
	});
}

const social = ($) => {
	jQuery("[id=sfsiid_email], [id=sfsiid_rss]")
		.closest(".sfsi_wicons.shuffeldiv").remove();

}

const getUrlParameter = (sParam) => {
	let sPageURL = window.location.search.substring(1),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;

	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
		}
	}
};

const directory = ($) => {
	if (!$('.post-type-archive-directory').length) return;
	let typeText = '';
	switch (getUrlParameter('type')) {
		case 'asociacion':
			typeText = 'Directorio de Asociaciones';
			break;
		case 'imss':
			typeText = 'Directorio IMSS';
			break;
		case 'ssa':
			typeText = 'Directorio SSA';
			break;
		case 'issste':
			typeText = 'Directorio ISSSTE';
			break;
		default:
			typeText = 'Directorio';
			break;
	}
	$('h1').text(typeText);
}

const main = ($) => {
	formComplaint($);
	likes($);
	starsFields($);
	filters($);
	menuDropdown($);
	directory($);
	/**
	 * the dependence order enqueue from wp functions won't work
	 * with the bootstrap theme script,
	 * then I have to use settimeout
	 */
	setTimeout(() => {
		wpcf7Custom($);
	}, 200);

	owlInit($);

	formComplaintSubmit($);
	social($);
}

main(jQuery);
