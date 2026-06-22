(function($){

    FLBuilder.registerModuleHelper('testimonials', {

        submit: function () {
            var form = $('.fl-builder-settings'),
                transition = parseFloat(form.find('input[name=speed]').val()) * 1000,
                delay = parseFloat(form.find('input[name=pause]').val()) * 1000;

            if (transition >= delay) {
                FLBuilder.alert( FLBuilderStrings.testimonialsTransitionWarn );
                return false;
            }
            return true;
        }
    });

})(jQuery);
