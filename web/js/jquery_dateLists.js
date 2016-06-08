/*
 * jQuery dateDropDowns
 *
 * url		http://www.amdonnelly.co.uk/jquery/date-drop-down-lists
 * author	Alan Donnelly 2011
 * version	1.0.1
 * license	MIT and GPL licenses
 */
(function (a) {
    a.fn.dateDropDowns = function (k) {
        var g = {
                dateFormat: "dd-mm-yy",
                monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                yearStart: "1979",
                yearEnd: "2011"
            },
            k = a.extend(g, k);
        return this.each(function () {
            function l() {
                var c = d.getMonth() + 1,
                    p = "",
                    f = 1;
                _daysInMonth = m(c, d.getFullYear()) + 1;
                a("#" + e + "_list").children().length < _daysInMonth ? f = a("#" + e + "_list").children().length + 1 : a("#" + e + "_list").children().remove();
                for (_x = f; _x < _daysInMonth; _x++) c = d.getDate() == _x ? "selected='true'" : "", p += "<option value='" + _x + "' " + c + ">" + _x + "</option>";
                a("#" + e + "_list").append(p)
            }

            function m(c, a) {
                var d = 31;
                c == 4 || c == 6 || c == 9 || c == 11 ? d = 30 : c == 2 && (d = a % 4 == 0 ? 29 : 28);
                return d
            }

            function n() {
                var c = d.getDate(),
                    a = d.getMonth() + 1,
                    f = d.getFullYear(),
                    b = g.dateFormat;
                b.indexOf("DD") > -1 && c.toString().length < 2 && (c = "0" + c);
                b.indexOf("MM") > -1 && a.toString().length < 2 && (a = "0" + a);
                b = g.dateFormat.toLowerCase();
                b = b.replace("dd", c);
                b = b.replace("mm", a);
                b = b.replace("yy", f);
                j.val(b)
            }
            var j = a(this);
            j.html();
            var b = j.attr("id") + "_dateLists",
                e = b + "_day",
                h = b + "_month",
                i = b + "_year",
                k = j.val(),
                d = new Date,
                o = g.dateFormat.indexOf("/") > -1 ? "/" : "-";
            (function () {
                if (k.length > 0) {
                    var a = g.dateFormat.split(o),
                        b = k.split(o),
                        f = new Date;
                    for (_x = 0; _x < b.length; _x++) a[_x].toLowerCase().indexOf("d") > -1 ? f.setDate(b[_x]) : a[_x].toLowerCase().indexOf("m") > -1 ? f.setMonth(b[_x] - 1) : a[_x].toLowerCase().indexOf("y") > -1 && f.setYear(b[_x]);
                    d = f
                }
            })();
            (function () {
                var c = g.dateFormat.split(o),
                    d = j;
                j.replaceWith("<div id='" + b + "' class='dateLists_container'></div>");
                for (_x = 0; _x < c.length; _x++) c[_x].toLowerCase().indexOf("d") > -1 ? (a("#" + b).append("<div id='" + e + "' class='day_container'>"), a("#" + e).append("<select id='" + e + "_list' name='" + e + "_list' class='list'></select>"), a("#" + b).append("</div>")) : c[_x].toLowerCase().indexOf("m") > -1 ? (a("#" + b).append("<div id='" + h + "' class='month_container'>"), a("#" + h).append("<select id='" + h + "_list' name='" + h + "_list' class='list'></select>"), a("#" + b).append("</div>")) : c[_x].toLowerCase().indexOf("y") > -1 && (a("#" + b).append("<div id='" + i + "' class='year_container'>"), a("#" + i).append("<select id='" + i + "_list' name='" + i + "_list' class='list'></select>"), a("#" + b).append("</div>"));
                a("#" + b).append(d);
                j.hide()
            })();
            l();
            (function () {
                a("#" + h + "_list").children().remove();
                for (_x = 0; _x < 12; _x++) {
                    var c = d.getMonth() == _x ? "selected='true'" : "";
                    a("#" + h + "_list").append("<option value='" + _x + "' " + c + ">" + g.monthNames[_x] + "</option>")
                }
            })();
            (function () {
                a("#" + i + "_list").children().remove();
                for (_x = parseInt(g.yearStart); _x < parseInt(g.yearEnd) + 1; _x++) {
                    var c = d.getFullYear() == _x ? "selected='true'" : "";
                    a("#" + i + "_list").append("<option value='" + _x + "' " + c + ">" + _x + "</option>")
                }
            })();
            (function () {
                a("#" + e + "_list").change(function () {
                    d.setDate(a("#" + e + "_list").val());
                    n()
                });
                a("#" + h + "_list").change(function () {
                    var c = parseInt(a("#" + h + "_list").val()),
                        b = d.getDate();
                    _daysInMonth = m(c + 1, d.getFullYear());
                    b > _daysInMonth && (b = _daysInMonth);
                    d = new Date(d.getFullYear(), c, b, 0, 0, 0, 0);
                    l();
                    n()
                });
                a("#" + i + "_list").change(function () {
                    var b = a("#" + i + "_list").val(),
                        e = d.getDate(),
                        f = d.getMonth();
                    _daysInMonth = m(f + 1, b);
                    e > _daysInMonth && (e = _daysInMonth);
                    d = new Date(b, f, e, 0, 0, 0, 0);
                    l();
                    n()
                })
            })()
        })
    }
})(jQuery);