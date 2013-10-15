(function(j) {
    j.fn.extend({
        accordion: function() {
            return this.each(function() {
                function b(c, b) {                    
                    // j(c).parent(d).siblings().removeClass(e).children(f).slideUp(g);
                    j(c).siblings(f)[b || h](b == "show" ? g : !1, function() {
                        j(c).siblings(f).is(":visible") ? j(c).parents(d).not(a.parents()).addClass(e) : j(c).parent(d).removeClass(e);
                        b == "show" && j(c).parents(d).not(a.parents()).addClass(e);
                        j(c).parents().show()
                    })
                }
                var a = j(this),
                    e = "active",
                    h = "slideToggle",
                    f = "ul, div",
                    g = "fast",
                    d = "li";
                if (a.data("accordiated")) return !1;
                j.each(a.find("ul, li>div"), function() {
                    j(this).data("accordiated", !0);
                    j(this).hide()
                });
                j.each(a.find("a"), function() {
                    j(this).click(function() {
                        b(this, h)
                    });
                    j(this).bind("activate-node", function() {
                        a.find(f).not(j(this).parents()).not(j(this).siblings()).slideUp(g);
                        b(this, "slideDown")
                    })
                });
                var i = location.hash ? a.find("a[href=" + location.hash + "]")[0] : a.find("li.current a")[0];
                i && b(i, !1)
            })
        }
    })
})(jQuery);