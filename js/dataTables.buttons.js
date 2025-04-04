/*!
 Buttons for DataTables 1.6.4
 ©2016-2020 SpryMedia Ltd - datatables.net/license
*/
(function (e) {
         "function" === typeof define && define.amd
                  ? define(["jquery", "datatables.net"], function (A) {
                             return e(A, window, document);
                    })
                  : "object" === typeof exports
                  ? (module.exports = function (A, z) {
                             A || (A = window);
                             (z && z.fn.dataTable) || (z = require("datatables.net")(A, z).$);
                             return e(z, A, A.document);
                    })
                  : e(jQuery, window, document);
})(function (e, A, z, u) {
         function E(a, b, c) {
                  e.fn.animate ? a.stop().fadeIn(b, c) : (a.css("display", "block"), c && c.call(a));
         }
         function F(a, b, c) {
                  e.fn.animate ? a.stop().fadeOut(b, c) : (a.css("display", "none"), c && c.call(a));
         }
         function H(a, b) {
                  a = new t.Api(a);
                  b = b ? b : a.init().buttons || t.defaults.buttons;
                  return new w(a, b).container();
         }
         var t = e.fn.dataTable,
                  M = 0,
                  N = 0,
                  x = t.ext.buttons,
                  w = function (a, b) {
                           if (!(this instanceof w))
                                    return function (c) {
                                             return new w(c, a).container();
                                    };
                           "undefined" === typeof b && (b = {});
                           !0 === b && (b = {});
                           Array.isArray(b) && (b = { buttons: b });
                           this.c = e.extend(!0, {}, w.defaults, b);
                           b.buttons && (this.c.buttons = b.buttons);
                           this.s = { dt: new t.Api(a), buttons: [], listenKeys: "", namespace: "dtb" + M++ };
                           this.dom = { container: e("<" + this.c.dom.container.tag + "/>").addClass(this.c.dom.container.className) };
                           this._constructor();
                  };
         e.extend(w.prototype, {
                  action: function (a, b) {
                           a = this._nodeToButton(a);
                           if (b === u) return a.conf.action;
                           a.conf.action = b;
                           return this;
                  },
                  active: function (a, b) {
                           var c = this._nodeToButton(a);
                           a = this.c.dom.button.active;
                           c = e(c.node);
                           if (b === u) return c.hasClass(a);
                           c.toggleClass(a, b === u ? !0 : b);
                           return this;
                  },
                  add: function (a, b) {
                           var c = this.s.buttons;
                           if ("string" === typeof b) {
                                    b = b.split("-");
                                    var d = this.s;
                                    c = 0;
                                    for (var h = b.length - 1; c < h; c++) d = d.buttons[1 * b[c]];
                                    c = d.buttons;
                                    b = 1 * b[b.length - 1];
                           }
                           this._expandButton(c, a, d !== u, b);
                           this._draw();
                           return this;
                  },
                  container: function () {
                           return this.dom.container;
                  },
                  disable: function (a) {
                           a = this._nodeToButton(a);
                           e(a.node).addClass(this.c.dom.button.disabled).attr("disabled", !0);
                           return this;
                  },
                  destroy: function () {
                           e("body").off("keyup." + this.s.namespace);
                           var a = this.s.buttons.slice(),
                                    b;
                           var c = 0;
                           for (b = a.length; c < b; c++) this.remove(a[c].node);
                           this.dom.container.remove();
                           a = this.s.dt.settings()[0];
                           c = 0;
                           for (b = a.length; c < b; c++)
                                    if (a.inst === this) {
                                             a.splice(c, 1);
                                             break;
                                    }
                           return this;
                  },
                  enable: function (a, b) {
                           if (!1 === b) return this.disable(a);
                           a = this._nodeToButton(a);
                           e(a.node).removeClass(this.c.dom.button.disabled).removeAttr("disabled");
                           return this;
                  },
                  name: function () {
                           return this.c.name;
                  },
                  node: function (a) {
                           if (!a) return this.dom.container;
                           a = this._nodeToButton(a);
                           return e(a.node);
                  },
                  processing: function (a, b) {
                           var c = this.s.dt,
                                    d = this._nodeToButton(a);
                           if (b === u) return e(d.node).hasClass("processing");
                           e(d.node).toggleClass("processing", b);
                           e(c.table().node()).triggerHandler("buttons-processing.dt", [b, c.button(a), c, e(a), d.conf]);
                           return this;
                  },
                  remove: function (a) {
                           var b = this._nodeToButton(a),
                                    c = this._nodeToHost(a),
                                    d = this.s.dt;
                           if (b.buttons.length) for (var h = b.buttons.length - 1; 0 <= h; h--) this.remove(b.buttons[h].node);
                           b.conf.destroy && b.conf.destroy.call(d.button(a), d, e(a), b.conf);
                           this._removeKey(b.conf);
                           e(b.node).remove();
                           a = e.inArray(b, c);
                           c.splice(a, 1);
                           return this;
                  },
                  text: function (a, b) {
                           var c = this._nodeToButton(a);
                           a = this.c.dom.collection.buttonLiner;
                           a = c.inCollection && a && a.tag ? a.tag : this.c.dom.buttonLiner.tag;
                           var d = this.s.dt,
                                    h = e(c.node),
                                    f = function (k) {
                                             return "function" === typeof k ? k(d, h, c.conf) : k;
                                    };
                           if (b === u) return f(c.conf.text);
                           c.conf.text = b;
                           a ? h.children(a).html(f(b)) : h.html(f(b));
                           return this;
                  },
                  _constructor: function () {
                           var a = this,
                                    b = this.s.dt,
                                    c = b.settings()[0],
                                    d = this.c.buttons;
                           c._buttons || (c._buttons = []);
                           c._buttons.push({ inst: this, name: this.c.name });
                           for (var h = 0, f = d.length; h < f; h++) this.add(d[h]);
                           b.on("destroy", function (k, g) {
                                    g === c && a.destroy();
                           });
                           e("body").on("keyup." + this.s.namespace, function (k) {
                                    if (!z.activeElement || z.activeElement === z.body) {
                                             var g = String.fromCharCode(k.keyCode).toLowerCase();
                                             -1 !== a.s.listenKeys.toLowerCase().indexOf(g) && a._keypress(g, k);
                                    }
                           });
                  },
                  _addKey: function (a) {
                           a.key && (this.s.listenKeys += e.isPlainObject(a.key) ? a.key.key : a.key);
                  },
                  _draw: function (a, b) {
                           a || ((a = this.dom.container), (b = this.s.buttons));
                           a.children().detach();
                           for (var c = 0, d = b.length; c < d; c++) a.append(b[c].inserter), a.append(" "), b[c].buttons && b[c].buttons.length && this._draw(b[c].collection, b[c].buttons);
                  },
                  _expandButton: function (a, b, c, d) {
                           var h = this.s.dt,
                                    f = 0;
                           b = Array.isArray(b) ? b : [b];
                           for (var k = 0, g = b.length; k < g; k++) {
                                    var m = this._resolveExtends(b[k]);
                                    if (m)
                                             if (Array.isArray(m)) this._expandButton(a, m, c, d);
                                             else {
                                                      var l = this._buildButton(m, c);
                                                      l &&
                                                               (d !== u && null !== d ? (a.splice(d, 0, l), d++) : a.push(l),
                                                               l.conf.buttons && ((l.collection = e("<" + this.c.dom.collection.tag + "/>")), (l.conf._collection = l.collection), this._expandButton(l.buttons, l.conf.buttons, !0, d)),
                                                               m.init && m.init.call(h.button(l.node), h, e(l.node), m),
                                                               f++);
                                             }
                           }
                  },
                  _buildButton: function (a, b) {
                           var c = this.c.dom.button,
                                    d = this.c.dom.buttonLiner,
                                    h = this.c.dom.collection,
                                    f = this.s.dt,
                                    k = function (n) {
                                             return "function" === typeof n ? n(f, l, a) : n;
                                    };
                           b && h.button && (c = h.button);
                           b && h.buttonLiner && (d = h.buttonLiner);
                           if (a.available && !a.available(f, a)) return !1;
                           var g = function (n, p, v, y) {
                                    y.action.call(p.button(v), n, p, v, y);
                                    e(p.table().node()).triggerHandler("buttons-action.dt", [p.button(v), p, v, y]);
                           };
                           h = a.tag || c.tag;
                           var m = a.clickBlurs === u ? !0 : a.clickBlurs,
                                    l = e("<" + h + "/>")
                                             .addClass(c.className)
                                             .attr("tabindex", this.s.dt.settings()[0].iTabIndex)
                                             .attr("aria-controls", this.s.dt.table().node().id)
                                             .on("click.dtb", function (n) {
                                                      n.preventDefault();
                                                      !l.hasClass(c.disabled) && a.action && g(n, f, l, a);
                                                      m && l.trigger("blur");
                                             })
                                             .on("keyup.dtb", function (n) {
                                                      13 === n.keyCode && !l.hasClass(c.disabled) && a.action && g(n, f, l, a);
                                             });
                           "a" === h.toLowerCase() && l.attr("href", "#");
                           "button" === h.toLowerCase() && l.attr("type", "button");
                           d.tag
                                    ? ((h = e("<" + d.tag + "/>")
                                               .html(k(a.text))
                                               .addClass(d.className)),
                                      "a" === d.tag.toLowerCase() && h.attr("href", "#"),
                                      l.append(h))
                                    : l.html(k(a.text));
                           !1 === a.enabled && l.addClass(c.disabled);
                           a.className && l.addClass(a.className);
                           a.titleAttr && l.attr("title", k(a.titleAttr));
                           a.attr && l.attr(a.attr);
                           a.namespace || (a.namespace = ".dt-button-" + N++);
                           d =
                                    (d = this.c.dom.buttonContainer) && d.tag
                                             ? e("<" + d.tag + "/>")
                                                        .addClass(d.className)
                                                        .append(l)
                                             : l;
                           this._addKey(a);
                           this.c.buttonCreated && (d = this.c.buttonCreated(a, d));
                           return { conf: a, node: l.get(0), inserter: d, buttons: [], inCollection: b, collection: null };
                  },
                  _nodeToButton: function (a, b) {
                           b || (b = this.s.buttons);
                           for (var c = 0, d = b.length; c < d; c++) {
                                    if (b[c].node === a) return b[c];
                                    if (b[c].buttons.length) {
                                             var h = this._nodeToButton(a, b[c].buttons);
                                             if (h) return h;
                                    }
                           }
                  },
                  _nodeToHost: function (a, b) {
                           b || (b = this.s.buttons);
                           for (var c = 0, d = b.length; c < d; c++) {
                                    if (b[c].node === a) return b;
                                    if (b[c].buttons.length) {
                                             var h = this._nodeToHost(a, b[c].buttons);
                                             if (h) return h;
                                    }
                           }
                  },
                  _keypress: function (a, b) {
                           if (!b._buttonsHandled) {
                                    var c = function (d) {
                                             for (var h = 0, f = d.length; h < f; h++) {
                                                      var k = d[h].conf,
                                                               g = d[h].node;
                                                      k.key &&
                                                               (k.key === a
                                                                        ? ((b._buttonsHandled = !0), e(g).click())
                                                                        : !e.isPlainObject(k.key) ||
                                                                          k.key.key !== a ||
                                                                          (k.key.shiftKey && !b.shiftKey) ||
                                                                          (k.key.altKey && !b.altKey) ||
                                                                          (k.key.ctrlKey && !b.ctrlKey) ||
                                                                          (k.key.metaKey && !b.metaKey) ||
                                                                          ((b._buttonsHandled = !0), e(g).click()));
                                                      d[h].buttons.length && c(d[h].buttons);
                                             }
                                    };
                                    c(this.s.buttons);
                           }
                  },
                  _removeKey: function (a) {
                           if (a.key) {
                                    var b = e.isPlainObject(a.key) ? a.key.key : a.key;
                                    a = this.s.listenKeys.split("");
                                    b = e.inArray(b, a);
                                    a.splice(b, 1);
                                    this.s.listenKeys = a.join("");
                           }
                  },
                  _resolveExtends: function (a) {
                           var b = this.s.dt,
                                    c,
                                    d = function (g) {
                                             for (var m = 0; !e.isPlainObject(g) && !Array.isArray(g); ) {
                                                      if (g === u) return;
                                                      if ("function" === typeof g) {
                                                               if (((g = g(b, a)), !g)) return !1;
                                                      } else if ("string" === typeof g) {
                                                               if (!x[g]) throw "Unknown button type: " + g;
                                                               g = x[g];
                                                      }
                                                      m++;
                                                      if (30 < m) throw "Buttons: Too many iterations";
                                             }
                                             return Array.isArray(g) ? g : e.extend({}, g);
                                    };
                           for (a = d(a); a && a.extend; ) {
                                    if (!x[a.extend]) throw "Cannot extend unknown button type: " + a.extend;
                                    var h = d(x[a.extend]);
                                    if (Array.isArray(h)) return h;
                                    if (!h) return !1;
                                    var f = h.className;
                                    a = e.extend({}, h, a);
                                    f && a.className !== f && (a.className = f + " " + a.className);
                                    var k = a.postfixButtons;
                                    if (k) {
                                             a.buttons || (a.buttons = []);
                                             f = 0;
                                             for (c = k.length; f < c; f++) a.buttons.push(k[f]);
                                             a.postfixButtons = null;
                                    }
                                    if ((k = a.prefixButtons)) {
                                             a.buttons || (a.buttons = []);
                                             f = 0;
                                             for (c = k.length; f < c; f++) a.buttons.splice(f, 0, k[f]);
                                             a.prefixButtons = null;
                                    }
                                    a.extend = h.extend;
                           }
                           return a;
                  },
                  _popover: function (a, b, c) {
                           var d = this.c,
                                    h = e.extend(
                                             {
                                                      align: "button-left",
                                                      autoClose: !1,
                                                      background: !0,
                                                      backgroundClassName: "dt-button-background",
                                                      contentClassName: d.dom.collection.className,
                                                      collectionLayout: "",
                                                      collectionTitle: "",
                                                      dropup: !1,
                                                      fade: 400,
                                                      rightAlignClassName: "dt-button-right",
                                                      tag: d.dom.collection.tag,
                                             },
                                             c
                                    ),
                                    f = b.node(),
                                    k = function () {
                                             F(e(".dt-button-collection"), h.fade, function () {
                                                      e(this).detach();
                                             });
                                             e(b.buttons('[aria-haspopup="true"][aria-expanded="true"]').nodes()).attr("aria-expanded", "false");
                                             e("div.dt-button-background").off("click.dtb-collection");
                                             w.background(!1, h.backgroundClassName, h.fade, f);
                                             e("body").off(".dtb-collection");
                                             b.off("buttons-action.b-internal");
                                    };
                           !1 === a && k();
                           c = e(b.buttons('[aria-haspopup="true"][aria-expanded="true"]').nodes());
                           c.length && ((f = c.eq(0)), k());
                           c = e("<div/>").addClass("dt-button-collection").addClass(h.collectionLayout).css("display", "none");
                           a = e(a).addClass(h.contentClassName).attr("role", "menu").appendTo(c);
                           f.attr("aria-expanded", "true");
                           f.parents("body")[0] !== z.body && (f = z.body.lastChild);
                           h.collectionTitle && c.prepend('<div class="dt-button-collection-title">' + h.collectionTitle + "</div>");
                           E(c.insertAfter(f), h.fade);
                           d = e(b.table().container());
                           var g = c.css("position");
                           "dt-container" === h.align && ((f = f.parent()), c.css("width", d.width()));
                           if ("absolute" === g && (c.hasClass(h.rightAlignClassName) || c.hasClass(h.leftAlignClassName) || "dt-container" === h.align)) {
                                    var m = f.position();
                                    c.css({ top: m.top + f.outerHeight(), left: m.left });
                                    var l = c.outerHeight(),
                                             n = d.offset().top + d.height(),
                                             p = m.top + f.outerHeight() + l;
                                    n = p - n;
                                    p = m.top - l;
                                    var v = d.offset().top,
                                             y = m.top - l - 5;
                                    (n > v - p || h.dropup) && -y < v && c.css("top", y);
                                    m = d.offset().left;
                                    d = d.width();
                                    d = m + d;
                                    g = c.offset().left;
                                    var q = c.width();
                                    q = g + q;
                                    var r = f.offset().left,
                                             B = f.outerWidth();
                                    B = r + B;
                                    r = 0;
                                    c.hasClass(h.rightAlignClassName)
                                             ? ((r = B - q), m > g + r && ((g = m - (g + r)), (d -= q + r), (r = g > d ? r + d : r + g)))
                                             : ((r = m - g), d < q + r && ((g = m - (g + r)), (d -= q + r), (r = g > d ? r + d : r + g)));
                                    c.css("left", c.position().left + r);
                           } else
                                    "absolute" === g
                                             ? ((m = f.position()),
                                               c.css({ top: m.top + f.outerHeight(), left: m.left }),
                                               (l = c.outerHeight()),
                                               (g = f.offset().top),
                                               (r = 0),
                                               (r = f.offset().left),
                                               (B = f.outerWidth()),
                                               (B = r + B),
                                               (g = c.offset().left),
                                               (q = a.width()),
                                               (q = g + q),
                                               (y = m.top - l - 5),
                                               (n = d.offset().top + d.height()),
                                               (p = m.top + f.outerHeight() + l),
                                               (n = p - n),
                                               (p = m.top - l),
                                               (v = d.offset().top),
                                               (n > v - p || h.dropup) && -y < v && c.css("top", y),
                                               (r = "button-right" === h.align ? B - q : r - g),
                                               c.css("left", c.position().left + r))
                                             : ((g = c.height() / 2), g > e(A).height() / 2 && (g = e(A).height() / 2), c.css("marginTop", -1 * g));
                           h.background && w.background(!0, h.backgroundClassName, h.fade, f);
                           e("div.dt-button-background").on("click.dtb-collection", function () {});
                           e("body")
                                    .on("click.dtb-collection", function (C) {
                                             var I = e.fn.addBack ? "addBack" : "andSelf",
                                                      J = e(C.target).parent()[0];
                                             ((!e(C.target).parents()[I]().filter(a).length && !e(J).hasClass("dt-buttons")) || e(C.target).hasClass("dt-button-background")) && k();
                                    })
                                    .on("keyup.dtb-collection", function (C) {
                                             27 === C.keyCode && k();
                                    });
                           h.autoClose &&
                                    setTimeout(function () {
                                             b.on("buttons-action.b-internal", function (C, I, J, O) {
                                                      O[0] !== f[0] && k();
                                             });
                                    }, 0);
                           e(c).trigger("buttons-popover.dt");
                  },
         });
         w.background = function (a, b, c, d) {
                  c === u && (c = 400);
                  d || (d = z.body);
                  a
                           ? E(e("<div/>").addClass(b).css("display", "none").insertAfter(d), c)
                           : F(e("div." + b), c, function () {
                                      e(this).removeClass(b).remove();
                             });
         };
         w.instanceSelector = function (a, b) {
                  if (a === u || null === a)
                           return e.map(b, function (f) {
                                    return f.inst;
                           });
                  var c = [],
                           d = e.map(b, function (f) {
                                    return f.name;
                           }),
                           h = function (f) {
                                    if (Array.isArray(f)) for (var k = 0, g = f.length; k < g; k++) h(f[k]);
                                    else "string" === typeof f ? (-1 !== f.indexOf(",") ? h(f.split(",")) : ((f = e.inArray(f.trim(), d)), -1 !== f && c.push(b[f].inst))) : "number" === typeof f && c.push(b[f].inst);
                           };
                  h(a);
                  return c;
         };
         w.buttonSelector = function (a, b) {
                  for (
                           var c = [],
                                    d = function (g, m, l) {
                                             for (var n, p, v = 0, y = m.length; v < y; v++) if ((n = m[v])) (p = l !== u ? l + v : v + ""), g.push({ node: n.node, name: n.conf.name, idx: p }), n.buttons && d(g, n.buttons, p + "-");
                                    },
                                    h = function (g, m) {
                                             var l,
                                                      n = [];
                                             d(n, m.s.buttons);
                                             var p = e.map(n, function (v) {
                                                      return v.node;
                                             });
                                             if (Array.isArray(g) || g instanceof e) for (p = 0, l = g.length; p < l; p++) h(g[p], m);
                                             else if (null === g || g === u || "*" === g) for (p = 0, l = n.length; p < l; p++) c.push({ inst: m, node: n[p].node });
                                             else if ("number" === typeof g) c.push({ inst: m, node: m.s.buttons[g].node });
                                             else if ("string" === typeof g)
                                                      if (-1 !== g.indexOf(",")) for (n = g.split(","), p = 0, l = n.length; p < l; p++) h(n[p].trim(), m);
                                                      else if (g.match(/^\d+(\-\d+)*$/))
                                                               (p = e.map(n, function (v) {
                                                                        return v.idx;
                                                               })),
                                                                        c.push({ inst: m, node: n[e.inArray(g, p)].node });
                                                      else if (-1 !== g.indexOf(":name")) for (g = g.replace(":name", ""), p = 0, l = n.length; p < l; p++) n[p].name === g && c.push({ inst: m, node: n[p].node });
                                                      else
                                                               e(p)
                                                                        .filter(g)
                                                                        .each(function () {
                                                                                 c.push({ inst: m, node: this });
                                                                        });
                                             else "object" === typeof g && g.nodeName && ((n = e.inArray(g, p)), -1 !== n && c.push({ inst: m, node: p[n] }));
                                    },
                                    f = 0,
                                    k = a.length;
                           f < k;
                           f++
                  )
                           h(b, a[f]);
                  return c;
         };
         w.defaults = {
                  buttons: ["copy", "excel", "csv", "pdf", "print"],
                  name: "main",
                  tabIndex: 0,
                  dom: {
                           container: { tag: "div", className: "dt-buttons" },
                           collection: { tag: "div", className: "" },
                           button: { tag: "ActiveXObject" in A ? "a" : "button", className: "dt-button", active: "active", disabled: "disabled" },
                           buttonLiner: { tag: "span", className: "" },
                  },
         };
         w.version = "1.6.4";
         e.extend(x, {
                  collection: {
                           text: function (a) {
                                    return a.i18n("buttons.collection", "Collection");
                           },
                           className: "buttons-collection",
                           init: function (a, b, c) {
                                    b.attr("aria-expanded", !1);
                           },
                           action: function (a, b, c, d) {
                                    a.stopPropagation();
                                    d._collection.parents("body").length ? this.popover(!1, d) : this.popover(d._collection, d);
                           },
                           attr: { "aria-haspopup": !0 },
                  },
                  copy: function (a, b) {
                           if (x.copyHtml5) return "copyHtml5";
                           if (x.copyFlash && x.copyFlash.available(a, b)) return "copyFlash";
                  },
                  csv: function (a, b) {
                           if (x.csvHtml5 && x.csvHtml5.available(a, b)) return "csvHtml5";
                           if (x.csvFlash && x.csvFlash.available(a, b)) return "csvFlash";
                  },
                  excel: function (a, b) {
                           if (x.excelHtml5 && x.excelHtml5.available(a, b)) return "excelHtml5";
                           if (x.excelFlash && x.excelFlash.available(a, b)) return "excelFlash";
                  },
                  pdf: function (a, b) {
                           if (x.pdfHtml5 && x.pdfHtml5.available(a, b)) return "pdfHtml5";
                           if (x.pdfFlash && x.pdfFlash.available(a, b)) return "pdfFlash";
                  },
                  pageLength: function (a) {
                           a = a.settings()[0].aLengthMenu;
                           var b = Array.isArray(a[0]) ? a[0] : a,
                                    c = Array.isArray(a[0]) ? a[1] : a;
                           return {
                                    extend: "collection",
                                    text: function (d) {
                                             return d.i18n("buttons.pageLength", { "-1": "Show all rows", _: "Show %d rows" }, d.page.len());
                                    },
                                    className: "buttons-page-length",
                                    autoClose: !0,
                                    buttons: e.map(b, function (d, h) {
                                             return {
                                                      text: c[h],
                                                      className: "button-page-length",
                                                      action: function (f, k) {
                                                               k.page.len(d).draw();
                                                      },
                                                      init: function (f, k, g) {
                                                               var m = this;
                                                               k = function () {
                                                                        m.active(f.page.len() === d);
                                                               };
                                                               f.on("length.dt" + g.namespace, k);
                                                               k();
                                                      },
                                                      destroy: function (f, k, g) {
                                                               f.off("length.dt" + g.namespace);
                                                      },
                                             };
                                    }),
                                    init: function (d, h, f) {
                                             var k = this;
                                             d.on("length.dt" + f.namespace, function () {
                                                      k.text(f.text);
                                             });
                                    },
                                    destroy: function (d, h, f) {
                                             d.off("length.dt" + f.namespace);
                                    },
                           };
                  },
         });
         t.Api.register("buttons()", function (a, b) {
                  b === u && ((b = a), (a = u));
                  this.selector.buttonGroup = a;
                  var c = this.iterator(
                           !0,
                           "table",
                           function (d) {
                                    if (d._buttons) return w.buttonSelector(w.instanceSelector(a, d._buttons), b);
                           },
                           !0
                  );
                  c._groupSelector = a;
                  return c;
         });
         t.Api.register("button()", function (a, b) {
                  a = this.buttons(a, b);
                  1 < a.length && a.splice(1, a.length);
                  return a;
         });
         t.Api.registerPlural("buttons().active()", "button().active()", function (a) {
                  return a === u
                           ? this.map(function (b) {
                                      return b.inst.active(b.node);
                             })
                           : this.each(function (b) {
                                      b.inst.active(b.node, a);
                             });
         });
         t.Api.registerPlural("buttons().action()", "button().action()", function (a) {
                  return a === u
                           ? this.map(function (b) {
                                      return b.inst.action(b.node);
                             })
                           : this.each(function (b) {
                                      b.inst.action(b.node, a);
                             });
         });
         t.Api.register(["buttons().enable()", "button().enable()"], function (a) {
                  return this.each(function (b) {
                           b.inst.enable(b.node, a);
                  });
         });
         t.Api.register(["buttons().disable()", "button().disable()"], function () {
                  return this.each(function (a) {
                           a.inst.disable(a.node);
                  });
         });
         t.Api.registerPlural("buttons().nodes()", "button().node()", function () {
                  var a = e();
                  e(
                           this.each(function (b) {
                                    a = a.add(b.inst.node(b.node));
                           })
                  );
                  return a;
         });
         t.Api.registerPlural("buttons().processing()", "button().processing()", function (a) {
                  return a === u
                           ? this.map(function (b) {
                                      return b.inst.processing(b.node);
                             })
                           : this.each(function (b) {
                                      b.inst.processing(b.node, a);
                             });
         });
         t.Api.registerPlural("buttons().text()", "button().text()", function (a) {
                  return a === u
                           ? this.map(function (b) {
                                      return b.inst.text(b.node);
                             })
                           : this.each(function (b) {
                                      b.inst.text(b.node, a);
                             });
         });
         t.Api.registerPlural("buttons().trigger()", "button().trigger()", function () {
                  return this.each(function (a) {
                           a.inst.node(a.node).trigger("click");
                  });
         });
         t.Api.register("button().popover()", function (a, b) {
                  return this.map(function (c) {
                           return c.inst._popover(a, this.button(this[0].node), b);
                  });
         });
         t.Api.register("buttons().containers()", function () {
                  var a = e(),
                           b = this._groupSelector;
                  this.iterator(!0, "table", function (c) {
                           if (c._buttons) {
                                    c = w.instanceSelector(b, c._buttons);
                                    for (var d = 0, h = c.length; d < h; d++) a = a.add(c[d].container());
                           }
                  });
                  return a;
         });
         t.Api.register("buttons().container()", function () {
                  return this.containers().eq(0);
         });
         t.Api.register("button().add()", function (a, b) {
                  var c = this.context;
                  c.length && ((c = w.instanceSelector(this._groupSelector, c[0]._buttons)), c.length && c[0].add(b, a));
                  return this.button(this._groupSelector, a);
         });
         t.Api.register("buttons().destroy()", function () {
                  this.pluck("inst")
                           .unique()
                           .each(function (a) {
                                    a.destroy();
                           });
                  return this;
         });
         t.Api.registerPlural("buttons().remove()", "buttons().remove()", function () {
                  this.each(function (a) {
                           a.inst.remove(a.node);
                  });
                  return this;
         });
         var D;
         t.Api.register("buttons.info()", function (a, b, c) {
                  var d = this;
                  if (!1 === a)
                           return (
                                    this.off("destroy.btn-info"),
                                    F(e("#datatables_buttons_info"), 400, function () {
                                             e(this).remove();
                                    }),
                                    clearTimeout(D),
                                    (D = null),
                                    this
                           );
                  D && clearTimeout(D);
                  e("#datatables_buttons_info").length && e("#datatables_buttons_info").remove();
                  a = a ? "<h2>" + a + "</h2>" : "";
                  E(
                           e('<div id="datatables_buttons_info" class="dt-button-info"/>')
                                    .html(a)
                                    .append(e("<div/>")["string" === typeof b ? "html" : "append"](b))
                                    .css("display", "none")
                                    .appendTo("body")
                  );
                  c !== u &&
                           0 !== c &&
                           (D = setTimeout(function () {
                                    d.buttons.info(!1);
                           }, c));
                  this.on("destroy.btn-info", function () {
                           d.buttons.info(!1);
                  });
                  return this;
         });
         t.Api.register("buttons.exportData()", function (a) {
                  if (this.context.length) return P(new t.Api(this.context[0]), a);
         });
         t.Api.register("buttons.exportInfo()", function (a) {
                  a || (a = {});
                  var b = a;
                  var c = "*" === b.filename && "*" !== b.title && b.title !== u && null !== b.title && "" !== b.title ? b.title : b.filename;
                  "function" === typeof c && (c = c());
                  c === u || null === c
                           ? (c = null)
                           : (-1 !== c.indexOf("*") && (c = c.replace("*", e("head > title").text()).trim()), (c = c.replace(/[^a-zA-Z0-9_\u00A1-\uFFFF\.,\-_ !\(\)]/g, "")), (b = G(b.extension)) || (b = ""), (c += b));
                  b = G(a.title);
                  b = null === b ? null : -1 !== b.indexOf("*") ? b.replace("*", e("head > title").text() || "Exported data") : b;
                  return { filename: c, title: b, messageTop: K(this, a.message || a.messageTop, "top"), messageBottom: K(this, a.messageBottom, "bottom") };
         });
         var G = function (a) {
                           return null === a || a === u ? null : "function" === typeof a ? a() : a;
                  },
                  K = function (a, b, c) {
                           b = G(b);
                           if (null === b) return null;
                           a = e("caption", a.table().container()).eq(0);
                           return "*" === b ? (a.css("caption-side") !== c ? null : a.length ? a.text() : "") : b;
                  },
                  L = e("<textarea/>")[0],
                  P = function (a, b) {
                           var c = e.extend(
                                             !0,
                                             {},
                                             {
                                                      rows: null,
                                                      columns: "",
                                                      modifier: { search: "applied", order: "applied" },
                                                      orthogonal: "display",
                                                      stripHtml: !0,
                                                      stripNewlines: !0,
                                                      decodeEntities: !0,
                                                      trim: !0,
                                                      format: {
                                                               header: function (q) {
                                                                        return d(q);
                                                               },
                                                               footer: function (q) {
                                                                        return d(q);
                                                               },
                                                               body: function (q) {
                                                                        return d(q);
                                                               },
                                                      },
                                                      customizeData: null,
                                             },
                                             b
                                    ),
                                    d = function (q) {
                                             if ("string" !== typeof q) return q;
                                             q = q.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "");
                                             q = q.replace(/<!\-\-.*?\-\->/g, "");
                                             c.stripHtml && (q = q.replace(/<([^>'"]*('[^']*'|"[^"]*")?)*>/g, ""));
                                             c.trim && (q = q.replace(/^\s+|\s+$/g, ""));
                                             c.stripNewlines && (q = q.replace(/\n/g, " "));
                                             c.decodeEntities && ((L.innerHTML = q), (q = L.value));
                                             return q;
                                    };
                           b = a
                                    .columns(c.columns)
                                    .indexes()
                                    .map(function (q) {
                                             var r = a.column(q).header();
                                             return c.format.header(r.innerHTML, q, r);
                                    })
                                    .toArray();
                           var h = a.table().footer()
                                             ? a
                                                        .columns(c.columns)
                                                        .indexes()
                                                        .map(function (q) {
                                                                 var r = a.column(q).footer();
                                                                 return c.format.footer(r ? r.innerHTML : "", q, r);
                                                        })
                                                        .toArray()
                                             : null,
                                    f = e.extend({}, c.modifier);
                           a.select && "function" === typeof a.select.info && f.selected === u && a.rows(c.rows, e.extend({ selected: !0 }, f)).any() && e.extend(f, { selected: !0 });
                           f = a.rows(c.rows, f).indexes().toArray();
                           var k = a.cells(f, c.columns);
                           f = k.render(c.orthogonal).toArray();
                           k = k.nodes().toArray();
                           for (var g = b.length, m = [], l = 0, n = 0, p = 0 < g ? f.length / g : 0; n < p; n++) {
                                    for (var v = [g], y = 0; y < g; y++) (v[y] = c.format.body(f[l], n, y, k[l])), l++;
                                    m[n] = v;
                           }
                           b = { header: b, footer: h, body: m };
                           c.customizeData && c.customizeData(b);
                           return b;
                  };
         e.fn.dataTable.Buttons = w;
         e.fn.DataTable.Buttons = w;
         e(z).on("init.dt plugin-init.dt", function (a, b) {
                  "dt" === a.namespace && (a = b.oInit.buttons || t.defaults.buttons) && !b._buttons && new w(b, a).container();
         });
         t.ext.feature.push({ fnInit: H, cFeature: "B" });
         t.ext.features && t.ext.features.register("buttons", H);
         return w;
});
