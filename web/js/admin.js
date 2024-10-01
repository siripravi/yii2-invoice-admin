/**
 * Admin theme JS hooks.
 */

(function ($) {
    'use strict';
    
    var Cookie = {
        write: function (name, value, days) {
            var expires;
            
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            } else {
                expires = "";
            }
            
            document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
        },
        
        read: function () {
            var nameEQ = encodeURIComponent(name) + "=",
                ca = document.cookie.split(';');
                
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1, c.length);
                }
                if (c.indexOf(nameEQ) === 0) {
                    return decodeURIComponent(c.substring(nameEQ.length, c.length));
                }
            }
            
            return null;
        },
        
        delete: function (name) {
            this.write(name, '', -1);
        }
    };
    
    var Modal = {
        
        remote: function (el, url) {
            $(el).on('show.bs.modal', function (e) {
                $(this).find('.modal-body').load(url);
            });
        },
        
        // Fix Pjax widget in Modal forms.
        pjax: function (el) {
            var $modal = $(el).find('.modal');

            function onBeforeSend(options, xhr) {
                if ($modal.is(':hidden')) {
                    $(document).off('pjax:beforeSend', onBeforeSend);
                    return;
                }
                $modal.on('hidden.bs.modal', function (e) {
                    $(document).off('pjax:beforeSend', onBeforeSend);
                    if ($(options.relatedTarget).is('form')) {
                        $(options.relatedTarget).submit();
                    }
                });
                $modal.modal('hide');
                return false;
            }

            if ($modal.length) {
                $(document).on('pjax:beforeSend', onBeforeSend);
            }
        }
        
    };
    
    var Grid = {
        
        /**
         * Selection column suitable for iCheck checkboxes.
         */
        initSelectionColumn: function (grid, options) {
            var $grid = $(grid);
            var id = $grid.attr('id');
            $grid.find('input').iCheck({
                handle: 'checkbox',
                checkboxClass: options.checkboxClass || ''
            });
            if (!options.multiple || !options.checkAll) {
                return;
            }
            var checkAll = "#" + id + " input[name='" + options.checkAll + "']";
            var inputs = options.class ? "input." + options.class : "input[name='" + options.name + "']";
            $(checkAll)
                .on('ifChecked', function () {
                    $grid.find(inputs + ":enabled").iCheck('check');
                })
                .on('ifUnchecked', function () {
                    $grid.find(inputs + ":enabled").iCheck('uncheck');
                });
        }
        
    };
    
    function init() {
        // Keep state of sidebar in cookie.
        $(document).on('expanded.pushMenu', function () {
            Cookie.delete('SidebarPushMenu');
        });
        $(document).on('collapsed.pushMenu', function () {
            Cookie.write('SidebarPushMenu', 'collapsed');
        })
    }
    
    $(init);
    
    // Expose object to Admin namespace.
    window.Admin = {
        Cookie: Cookie,
        Modal: Modal,
        Grid: Grid
    };

}(jQuery));
