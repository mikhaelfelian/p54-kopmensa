(function($) {
    $.growl = function(options) {
        var defaults = {
            type: 'success',
            title: '',
            message: '',
            delay: 5000,
            allow_dismiss: true,
            offset: {
                x: 20,
                y: 85
            },
            spacing: 10,
            z_index: 1031,
            animate: {
                enter: 'animated fadeInRight',
                exit: 'animated fadeOutRight'
            }
        };

        var settings = $.extend({}, defaults, options);
        var $container = $('#growl-container');

        if ($container.length === 0) {
            $container = $('<div id="growl-container" class="growl"></div>');
            $('body').append($container);
        }

        var $item = $('<div class="growl-item ' + settings.type + '"></div>');
        
        if (settings.title) {
            $item.append('<div class="growl-title">' + settings.title + '</div>');
        }
        
        $item.append('<div class="growl-message">' + settings.message + '</div>');
        
        if (settings.allow_dismiss) {
            $item.append('<div class="growl-close">&times;</div>');
        }

        $container.append($item);

        $item.css({
            'margin-top': settings.offset.y + 'px',
            'margin-right': settings.offset.x + 'px',
            'z-index': settings.z_index
        });

        $item.addClass(settings.animate.enter);

        $item.find('.growl-close').on('click', function() {
            $item.removeClass(settings.animate.enter).addClass(settings.animate.exit);
            setTimeout(function() {
                $item.remove();
            }, 300);
        });

        if (settings.delay > 0) {
            setTimeout(function() {
                $item.removeClass(settings.animate.enter).addClass(settings.animate.exit);
                setTimeout(function() {
                    $item.remove();
                }, 300);
            }, settings.delay);
        }
    };

    $.growl.success = function(options) {
        if (typeof options === 'string') {
            options = { message: options };
        }
        options.type = 'success';
        $.growl(options);
    };

    $.growl.error = function(options) {
        if (typeof options === 'string') {
            options = { message: options };
        }
        options.type = 'alert';
        $.growl(options);
    };

    $.growl.warning = function(options) {
        if (typeof options === 'string') {
            options = { message: options };
        }
        options.type = 'warning';
        $.growl(options);
    };

    $.growl.info = function(options) {
        if (typeof options === 'string') {
            options = { message: options };
        }
        options.type = 'info';
        $.growl(options);
    };
})(jQuery); 