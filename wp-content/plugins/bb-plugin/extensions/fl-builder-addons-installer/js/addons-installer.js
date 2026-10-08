(function($) {

	var FLBuilderAddonsInstaller = {

		init: function() {

			$(document).on('click', '.fl-installer-addon-activate', function(e) {
				e.preventDefault();
				var wrap = $(this),
					type = $(this).data('type'),
					slug = $(this).data('slug');

				data = {
					'action': 'fl_addons_activate',
					'type': type,
					'slug': slug,
					'_wpnonce': $('.fl-downloads-list').find('#_wpnonce').val()
				}
				wrap.html('<strong>Please Wait</strong>');
				$.post(ajaxurl, data, function(response) {
					if (response.success) {
						wrap.fadeOut();
						wrap.replaceWith('<em>' + bb_addon_data.installed + '</em>').fadeIn();
						new Notify({
							status: 'success',
							title: bb_addon_data.activated,
							autoclose: true,
							autotimeout: 1000,
						}, location.reload() );
					}
				});
			});

			$(document).on('click', '.fl-installer-addon', function(e) {
				e.preventDefault();
				var wrap = $(this),
					type = $(this).data('type'),
					slug = $(this).data('slug'),
					isReinstall = wrap.hasClass('fl-reinstall-addon'),
					url = 'plugin' === type ? bb_addon_data.plugins_url : bb_addon_data.themes_url;

				// Confirm before reinstalling
				if ( isReinstall ) {
					var confirmMsg = 'bb-theme-child' === slug
						? 'Are you sure you want to reinstall the child theme? This will overwrite all child theme files and erase any customizations you have made.'
						: 'Are you sure you want to reinstall this product? This will overwrite the current installation.';
					if ( ! confirm( confirmMsg ) ) {
						return;
					}
				}

				data = {
					'action': 'fl_addons_install',
					'type': type,
					'slug': slug,
					'_wpnonce': $('.fl-downloads-list').find('#_wpnonce').val()
				}
				wrap.html('<strong>' + bb_addon_data.wait + '</strong>');
				$.post(ajaxurl, data, function(response) {
						if (response.success) {
							wrap.fadeOut();

							if ( isReinstall ) {
								wrap.replaceWith('<em>' + bb_addon_data.installed + '</em>').fadeIn();
							} else if ('plugin' === type) {
								if ( 'bb-theme-builder' === data.slug ) {
									wrap.replaceWith('<a class="fl-download-action fl-download-action-primary fl-installer-addon-activate" data-type="plugin" data-slug="bb-theme-builder/bb-theme-builder.php" href="#">Activate</a>');
								} else {
									wrap.replaceWith('<em>' + bb_addon_data.installed + '</em>').fadeIn()
								}
							} else {
								wrap.replaceWith('<a class="fl-download-action" href="' + url + '">' + bb_addon_data.activate + '</a>').fadeIn();
							}

							new Notify({
								status: 'success',
								title: bb_addon_data.installed,
								autoclose: true,
								autotimeout: 1000,
							});
							if ('bb-theme-child' === data.slug) {
								setTimeout(function(){
									window.location.reload();
								}, 1500);
							}
							if ( isReinstall ) {
								setTimeout(function(){
									window.location.reload();
								}, 1500);
							}
						} else {
							// build message
							var msg = '';
							$.each(response.data, function(i, e) {
								msg += e.message
							})
							new Notify({
								status: 'error',
								title: msg,
								autoclose: true,
								autotimeout: 5000,
							});
							wrap.html( isReinstall ? bb_addon_data.reinstall : bb_addon_data.install );
						}
					})
					.fail(function() {
						new Notify({
							status: 'error',
							title: 'Install Error',
							autoclose: true,
							autotimeout: 5000,
						});
						wrap.html( isReinstall ? bb_addon_data.reinstall : bb_addon_data.install );
					});
			});
		},
	}

	$(function() {
		new FLBuilderAddonsInstaller.init();
	})
})(jQuery);
