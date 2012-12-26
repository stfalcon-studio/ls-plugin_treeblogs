jQuery(document).ready(function () {
    jQuery('label[for="blog_id"]').parent('p').remove();

    jQuery('a.blogs-filter').live('click', function (event) {
        event.preventDefault();
        var url = window.location.search,
            newUrl = window.location.pathname,
            pattern = '',
            regexp = new RegExp('\/(([a-z0-9+\$_.-])+$)', 'i'),
            filter = regexp.exec(this.href)[1];

        // есть ли строка поиска
        if (url) {
            var position    = url.search('b='),
                position2   = url.search(filter);
            // есть ли етот фильтр
            if (position2 !== -1) {
                var regexpFilter    = new RegExp('b=(([a-z0-9+\$_.-]\+)+$)', 'i'),
                    filterUrl       = url.match(regexpFilter)[1],
                    position3       = filterUrl.search(/\+/);
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

        window.location = newUrl + url;
    });
});

var nxtGroup = 0;

/*Удаление группы*/
function delGroup(groupidx) {
    jQuery('#g' + groupidx).remove();
    return false;
}

/*устанавливаем значение blog_id и subblog_id*/
function setBlogId(group, blogid) {
    var groupIdx = parseInt(group.match(/\d+/), 10);
    if (groupIdx === 0) {
        jQuery('#blog_id').val(blogid);
    } else {
        jQuery('#subblog_id_' + groupIdx).val(blogid);
    }
}

/*заполняем select значениями*/
function populateSelector(select, group, nextLevel) {
    var sel = jQuery(group).find('#' + group + "_" + nextLevel);
    if (sel) {
        sel.remove();
    }
    jQuery('#' + group + ' a:first').before(select);
    sel = jQuery(group).find('#' + group + "_" + nextLevel + ' select');
    if (sel.val()) {
        setBlogId(group, sel.val());
    }
}

/*запрос к системе - выбрать значение для select */
function doBlogsRequest(action, blogid, group, nextLevel) {
    var params = {
        blogid: blogid,
        action: action,
        nextlevel: nextLevel,
        groupid: group,
        security_ls_key: LIVESTREET_SECURITY_KEY
    };
    ls.ajax(aRouter.ajax + 'treeblogs', params, function (result) {
        if (!result.noValue) {
            populateSelector(result.select, group, nextLevel);
        }
    }.bind(this));
}

/*Добавление новой группы*/
function addGroup() {
    var newGroup    = 'g' + nxtGroup,
        blogId      = -1,
        groupIdx    = nxtGroup,
        params = {
            groupIdx: groupIdx,
            action: 'newgroup',
            security_ls_key: LIVESTREET_SECURITY_KEY
        };
    ls.ajax(aRouter.ajax + 'treeblogs', params, function (result) {
        if (!result.noValue) {
            jQuery('#groups').append(result.select);
            nxtGroup++;
            doBlogsRequest('level', blogId, newGroup, 0);
        }
    }.bind(this));
    return false;
}

/*удаление элемента select*/
function removeSelects(group, fromlevel) {
    while (jQuery('#' + group + "_" + fromlevel).length) {
        jQuery('#' + group + "_" + fromlevel).remove();
        fromlevel++;
    }
}
/*Обработчик события change для єлементов select*/
function changeBlogSelector(select) {

    var blogid      = jQuery(select).val(),
        currLevel	= parseInt(jQuery(select).attr('name'), 10),
        group		= jQuery(select).parent('div').attr('id');
    removeSelects(group, currLevel + 1);

    doBlogsRequest('children', blogid, group, currLevel + 1);

    if (blogid == -1) {
        if (currLevel == 0) { /* first element in second group */
            setBlogId(group, -1);
        } else {
            var blogId = jQuery(group).find('#' + group + "_" + (currLevel - 1)).val();
            setBlogId(group, blogId);
        }
    } else {
        setBlogId(group, blogid);
    }

}

function reverseMenu(id) {
	var u = jQuery('#m' + id),
        d = jQuery('#d' + id);
	if (u.is(':visible')) {
		jQuery(u).attr('class', 'regular');
		jQuery(d).attr('class', 'regular');
	} else {
		jQuery(u).attr('class', 'active');
		jQuery(d).attr('class', 'active');
	}

}
