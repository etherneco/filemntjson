(function ($) {
    $.fn.tablesorter = function () {
        var $table = this;
        this.find('th').click(function () {
            var idElem = $(this).index();
            var direct = $(this).hasClass('sort_asc');
            $table.tablesortby(idElem, direct);
        });
        return this;
    };
    $.fn.tablesortby = function (idx, direction) {
        var $rows = this.find('tbody tr');
        function elementToVal(a) {
            var $a_elem = $(a).find('td:nth-child(' + (idx + 1) + ')');
            var a_val = $a_elem.attr('data-sort') || $a_elem.text();
            return (a_val == parseInt(a_val) ? parseInt(a_val) : a_val);
        }
        $rows.sort(function (a, b) {
            var a_val = elementToVal(a), b_val = elementToVal(b);
            return (a_val > b_val ? 1 : (a_val == b_val ? 0 : -1)) * (direction ? 1 : -1);
        })
        this.find('th').removeClass('sort_asc sort_desc');
        $(this).find('thead th:nth-child(' + (idx + 1) + ')').addClass(direction ? 'sort_desc' : 'sort_asc');
        for (var i = 0; i < $rows.length; i++)
            this.append($rows[i]);
        this.settablesortmarkers();
        return this;
    }
    $.fn.retablesort = function () {
        var $e = this.find('thead th.sort_asc, thead th.sort_desc');
        if ($e.length)
            this.tablesortby($e.index(), $e.hasClass('sort_desc'));

        return this;
    }
    $.fn.settablesortmarkers = function () {
        this.find('thead th span.indicator').remove();
        this.find('thead th.sort_asc').append('<span class="indicator">&darr;<span>');
        this.find('thead th.sort_desc').append('<span class="indicator">&uarr;<span>');
        return this;
    }
})(jQuery);



function renderBreadcrumbs(path) {
    var base = "",
            $html = $('<div/>').append($('<a href=#>Home</a></div>'));
    $.each(path.split('%2F'), function (k, v) {
        if (v) {
            var v_as_text = decodeURIComponent(v);
            $html.append($('<span/>').text(' ▸ '))
                    .append($('<a/>').attr('href', '#' + base + v).text(v_as_text));
            base += v + '%2F';
        }
    });
    return $html;
}

function formatTimestamp(unix_timestamp) {
    var m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var d = new Date(unix_timestamp * 1000);
    return [m[d.getMonth()], ' ', d.getDate(), ', ', d.getFullYear(), " ",
        (d.getHours() % 12 || 12), ":", (d.getMinutes() < 10 ? '0' : '') + d.getMinutes(),
        " ", d.getHours() >= 12 ? 'PM' : 'AM'].join('');
}
function formatFileSize(bytes) {
    var s = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
    for (var pos = 0; bytes >= 1000; pos++, bytes /= 1024)
        ;
    var d = Math.round(bytes * 10);
    return pos ? [parseInt(d / 10), ".", d % 10, " ", s[pos]].join('') : bytes + ' bytes';
}


