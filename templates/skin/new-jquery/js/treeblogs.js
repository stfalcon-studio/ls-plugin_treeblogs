jQuery(document).ready(function () {
    jQuery('a.blogs-filter').live('click', function (event) {
        event.preventDefault();
        var url = window.location.search,
            newUrl = window.location.pathname,
            pattern = '',
            regexp = new RegExp('\/(([a-z0-9+\$_.-])+$)', 'i'),
            filter = regexp.exec(this.href)[1];

        // есть ли строка поиска
        if (url) {
            var position = url.search('b='),
                position2 = url.search(filter);
            // есть ли етот фильтр
            if (position2 !== -1) {
                var regexpFilter = new RegExp('b=(([a-z0-9+\$_.-]\+)+$)', 'i'),
                    filterUrl = url.match(regexpFilter)[1],
                    position3 = filterUrl.search(/\+/);
                // этот фильтр один?
                if (position3 !== -1) {
                    // нет
                    pattern = '\\+' + filter;
                    var search = filterUrl.search(pattern);
                    if (search !== -1) {
                        pattern = '(\\+?' + filter + ')';
                        regexp = new RegExp(pattern);
                        url = url.replace(regexp,'');
                    } else {
                        pattern = '(' + filter + ')\\+?';
                        regexp = new RegExp(pattern);
                        url = url.replace(regexp,'');
                    }
                } else {
                    //да
                    pattern = '(\\??&?b=\\+?' + filter + ')';
                    regexp = new RegExp(pattern);
                    url = url.replace(regexp,'');
                }
            } else {
                if (position !== -1) {
                    url += '+' + filter;
                } else {
                    url += '&b=' + filter;
                }
            }
        } else {
            url += '?b=' + filter;
        }
        
        window.location = newUrl +url;
    });
});