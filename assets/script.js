(function($) {
    'use strict';

    function updateData(ev, data, format)
    {
	    var $el = $(ev.target);

	    var $widget = $el
	        .closest('.croppie-widget');

	    var res = $widget
	        .find('.croppie-widget__canvas')
	        .croppie('result', {
		        type: 'base64',
		        format: format,
		        circle: false,
	        })
	        .then(function(data) {
		        $widget.find('.croppie-widget__data').val(data);
		        $el.val('');
	        });
    }

    window.croppieWidget = {

	    onUploadChange: function(ev) {

	        var el = ev.target;

	        if (!(el.files && el.files.length)) {
		        alert('You need a recent browser to upload.');
		        return;
	        }

	        var fr = new FileReader();

	        fr.onload = function(e) {
		        var $el = $(el);
		        $el
		            .closest('.croppie-widget')
		            .find('.croppie-widget__canvas')
		            .croppie('bind', {
                        url: e.target.result
		            });
	        }

	        fr.readAsDataURL(el.files[0]);
	    },

	    updateData: updateData

    };

    $(function() {
	    $(document).on('click', '[data-croppie-rotate-deg]', function() {
	        var deg = parseFloat($(this).data('croppie-rotate-deg'));
	        $(this)
		        .closest('.croppie-widget')
		        .find('.croppie-widget__canvas')
		        .croppie('rotate', deg);
	    });
    });

})(jQuery);
