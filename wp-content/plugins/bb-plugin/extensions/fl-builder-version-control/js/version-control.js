(function($) {

	var FLBuilderAdvancedInstaller = {

		init: function() {
			$('.bb-plugin-install').on( 'click', function() {
				version = $('select.bb-plugin').val();
				flavour = $('input.flavour').val();
				args = {
					'slug': 'bb-plugin',
					'version': version,
					'flavour': flavour,
					'type': 'plugin'
				}
				FLBuilderAdvancedInstaller.install( args )
			});
			$('.bb-themer-install').on( 'click', function() {
				version  = $('select.bb-theme-builder').val();
				args = {
					'slug': 'bb-theme-builder',
					'version': version,
					'flavour': '',
					'type': 'plugin'
				}
				FLBuilderAdvancedInstaller.install( args);
			});
			$('.bb-theme-install').on( 'click', function() {
				version  = $('select.bb-theme').val();
				args = {
					'slug': 'bb-theme',
					'version': version,
					'flavour': '',
					'type': 'theme'
				}
				FLBuilderAdvancedInstaller.install( args);
			});
		},
		install: function( args ) {
			FLBuilderAdvancedInstaller.status( '<h2 class="loading">' + FLBuilderAdminVersionControl.installing + '</h2>')
			data = {
				'action': 'fl_version_control',
				'type': args.type,
				'slug': args.slug,
				'version': args.version,
				'flavour': args.flavour,
				'install_nonce': FLBuilderAdminVersionControl.nonce
			}

			$.post(ajaxurl, data, function(response) {
				if (response.success) {
					FLBuilderAdvancedInstaller.status();
					new Notify({
						status: 'success',
						title: 'Version Installed!',
						autoclose: true,
						autotimeout: 3000,
					},
					//location.reload()
				);
				} else {
					// build message
					var msg = '';
					if ( 'string' === typeof response.data ) {
						msg = response.data;
					} else {
						$.each(response.data, function(i, e) {
							msg += e.message
						})
					}
					new Notify({
						status: 'error',
						title: msg,
						autoclose: true,
						autotimeout: 5000,
					});
					FLBuilderAdvancedInstaller.status();
				}
			});
		},
		status: function(txt) {
			if ( ! txt ) {
				$('div.status').fadeOut()
			}
			$('div.status').show().html(txt)
		}
	}

	$(function() {
		new FLBuilderAdvancedInstaller.init();
	})
})(jQuery);
