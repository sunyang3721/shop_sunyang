!
function(a) {
	a.fn.editBox = function(b) {
		function z() {
			var b = g.find("#editBox_SelectBox"),
			c = g.find("#editBox_SelectBox .editBox");
			if (c.length > 0) for (var d = 0; d < c.length; d++) c.eq(d).prependTo(g),
			c.eq(d).css({
				left: parseInt(c.eq(d).css("left")) + parseInt(b.css("left")),
				top: parseInt(c.eq(d).css("top")) + parseInt(b.css("top"))
			});
			a("#editBox_SelectBox").remove()
		}
		var d,
		f,
		g,
		h,
		j,
		k,
		l,
		m,
		n,
		o,
		p,
		q,
		r,
		s,
		t,
		u,
		v,
		b = a.extend({
			id: "BoxCanvas",
			width: "auto",
			height: 450,
			zindex: 1,
			isAddbox: !1,
			isResize: !0,
			reisze: !1,
			isFocusbox: new Array,
			isBoxEdit: !1,
			selectbox: !1,
			color: "red",
			background: "st.jpg",
			_x: 0,
			_y: 0,
			offsetX: 0,
			offsetY: 0,
			maxX: 0,
			maxY: 0,
			minHeight: 360,
			minWidth: 700,
			imgurl: "images/editBox/",
			coloroption: ["red", "yellow", "green", "blue", "purple", "black"],
			fontsizeoption: ["20px", "18px", "16px", "14px", "12px", "10px"],
			textoption: ["\u6536\u4ef6\u4eba{Binding toname}", "\u6536\u4ef6\u4eba\u7535\u8bdd{Binding totel}", "\u6536\u4ef6\u4eba\u5730\u5740{Binding toadd}", "\u76ee\u7684\u5730{Binding tocity}", "\u5bc4\u4ef6\u4eba{Binding formname}", "\u5bc4\u4ef6\u4eba\u7535\u8bdd{Binding formtel}", "\u5bc4\u4ef6\u4eba\u5730\u5740{Binding formadd}", "\u59cb\u53d1\u5730{Binding formcity}", "\u65e5\u671f{Binding date}", "\u5546\u54c1\u540d\u79f0{Binding goods}", "\u91d1\u989d{Binding sum}", "\u5907\u6ce8{Binding memory}", "\u4fdd\u4ef7{Binding Insured}", "\u4ee3\u6536\u8d27\u6b3e{Binding payment}"],
			backgroundOption: ["\u987a\u4e30\u5feb\u9012,sf.jpg", "EMS,ems.jpg", "\u7533\u901a\u5feb\u9012,st.jpg", "\u98ce\u901f\u8fbe\u5feb\u9012\u98ce\u901f\u8fbe\u5feb\u9012,fsd.jpg"]
		},
		b),
		c = a(this);
		c.css("position", "relative");
		var w = function() {
			c.append("<div id='editTool' class='editTool'></div>"),
			c.append("<div class='editCanvas_bg'><ul class='staff_x'></ul><ul><li class='staff_y'></li><li><div id='editCanvas' class='editCanvas'></div></li></ul></div>"),
			c.append("<div id='editResize' class='editBottom'><ul></ul><ul class='br'></ul></div>"),
			g = a("#editCanvas"),
			h = a(".editCanvas_bg"),
			j = a(".staff_x"),
			k = a(".staff_y"),
			d = a("#editTool"),
			f = a("#editResize");
			var e = [];
			e.push("<ul id='addTool'><li><img src='" + b.imgurl + "btn_add.png' align='absmiddle'/></li><li>\u6dfb \u52a0</li></ul>"),
			e.push("<ul id='selectTool' class='current'><li>"),
			e.push("<img src='" + b.imgurl + "btn_select.png' align='absmiddle'/></li><li>\u9009 \u62e9</li></ul>"),
			e.push("<ul id='delTool'><li><img src='" + b.imgurl + "btn_del.png' align='absmiddle'/></li><li>\u5220 \u9664</li></ul>"),
			e.push("<ul id='alignTool'><li><img src='" + b.imgurl + "btn_align.png' align='absmiddle'/></li>"),
			e.push("<li>\u5bf9 \u9f50</li><img src='" + b.imgurl + "ico_arrow.png' align='right'/><ol><li id='left'>\u5de6\u5bf9\u9f50</li>"),
			e.push("<li id='right'>\u53f3\u5bf9\u9f50</li><li id='x-center'>\u6c34\u5e73\u5c45\u4e2d</li><li id='top'>\u4e0a\u5bf9\u9f50</li>"),
			e.push("<li id='bottom'>\u4e0b\u5bf9\u9f50</li><li id='y-center'>\u5782\u76f4\u5c45\u4e2d</li></ol></ul>"),
			e.push("<ul id='panelPerpro'>"),
			e.push("<li><img src='" + b.imgurl + "ico_x.png' align='absmiddle'/></li>"),
			e.push("<li><input id='panelX' value='0'/></li>"),
			e.push("<li><img src='" + b.imgurl + "ico_y.png' align='absmiddle'/></li>"),
			e.push("<li><input id='panelY' value='0'/></li>"),
			e.push("<li><img src='" + b.imgurl + "ico_width.png' align='absmiddle'/></li>"),
			e.push("<li><input id='panelWidth' value='0'/></li>"),
			e.push("<li><img src='" + b.imgurl + "ico_height.png' align='absmiddle'/></li>"),
			e.push("<li><input id='panelHeight' value='0'/></li>"),
			e.push("</ul><ul id='panelFontsize'><span>\u5b57\u53f7\uff1a</span>"),
			e.push("<img src='" + b.imgurl + "ico_arrow.png' class='arrow'/><ol></ol></ul>"),
			e.push("<ul id='panelText'><span>\u81ea\u5b9a\u4e49</span>"),
			e.push("<img src='" + b.imgurl + "ico_arrow.png' class='arrow'/><ol></ol></ul>"),
			e.push("<ul id='panelTemplate'><li>\u80cc\u666f\uff1a</li><span>\u65e0\u80cc\u666f</span>"),
			e.push("<img src='" + b.imgurl + "ico_arrow.png' class='arrow'/><ol></ol></ul>"),
			e.push("<ul id='panelColor'><li>\u63a7\u4ef6\u989c\u8272:</li><a></a>"),
			e.push("<img src='" + b.imgurl + "ico_arrow.png' class='arrow'/><ol></ol></ul>"),
			e.push("<ul id='panelPage'><li>\u9875\u9762\u8bbe\u7f6e</li><img src='" + b.imgurl + "ico_arrow.png' class='arrow'/><ol></ol><dl></dl></ul>"),
			e.push("<ul id='saveTool' notice='\u4fdd\u5b58\u8bbe\u8ba1\u597d\u7684\u6a21\u677f'><li>"),
			e.push("<img src='" + b.imgurl + "btn_save.png' align='absmiddle'/></li>"),
			e.push("<li>\u4fdd\u5b58\u6a21\u677f</li></ul>"),
			e.push("<div style='clear:both;'></div>"),
			d.html(e.join("\n")),
			e.length = 0,
			l = a("#addTool"),
			m = a("#selectTool"),
			n = a("#delTool"),
			o = a("#alignTool"),
			p = a("#saveTool"),
			s = a("#panelTemplate"),
			t = a("#panelColor"),
			d.css({
				width: b.width
			}),
			f.css({
				width: b.width
			}),
			q = a("#panelFontsize"),
			r = a("#panelText"),
			u = a("#panelPage"),
			h.css({
				width: b.width,
				height: b.height - parseInt(d.css("height")) - parseInt(f.css("height")),
				background: "url(" + b.imgurl + b.background + ") no-repeat 20px 20px"
			}),
			b.width = "auto" == b.width ? parseInt(c.width()) : b.width,
			g.css({
				width: b.width - 20,
				height: b.height - parseInt(d.css("height")) - parseInt(f.css("height")) - 20
			}),
			k.css({
				height: b.height - parseInt(d.css("height")) - parseInt(f.css("height")) - 20
			});
			for (var i = 0; i < b.fontsizeoption.length; i++) q.find("ol").append("<li id='" + b.fontsizeoption[i] + "'>" + b.fontsizeoption[i] + "</li>");
			for (var i = 0; i < b.textoption.length; i++) r.find("ol").append("<li id='" + b.textoption[i] + "'>" + b.textoption[i] + "</li>");
			for (var i = 0; i < b.coloroption.length; i++) t.find("ol").append("<li id='" + b.coloroption[i] + "'><b></b><span>" + b.coloroption[i] + "</span></li>");
			s.find("ol").append("<li id='noimage.jpg'>\u65e0\u80cc\u666f</li>");
			for (var i = 0; i < b.backgroundOption.length; i++) {
				var v = b.backgroundOption[i].split(",");
				s.find("ol").append("<li id='" + v[1] + "'>" + v[0] + "</li>")
			}
			for (var i = 0; 50 > i; i++) j.append("<span>" + i + "</span>"),
			k.append("<span>" + i + "</span>");
			b.offsetX = parseInt(g.offset().left),
			b.offsetY = parseInt(g.offset().top),
			e.length = 0,
			e.push("<li><span>\u9875\u9762\u5bbd\u5ea6\uff1a<input value='22.9' />cm</span></li>"),
			e.push("<li><span>\u9875\u9762\u9ad8\u5ea6\uff1a<input value='12.6' />cm</span></li>"),
			e.push("<li><span>\u6253\u5370\u504f\u79fbx\uff1a<input value='0' />cm</span></li>"),
			e.push("<li><span>\u6253\u5370\u504f\u79fby\uff1a<input value='0' />cm</span></li>"),
			u.find("ol").html(e.join("\n")),
			d.find("img").addClass("png")
		};
		w(),
		a.fn.addBox = function(c) {
			var c = a.extend({
				id: "autobox",
				text: "\u6587\u672c\u5185\u5bb9",
				width: 200,
				height: 45,
				top: 100,
				left: 100,
				isMove: !1,
				isResize: !1,
				isEdit: !1,
				resizeType: "none",
				minWidth: 50,
				minHeight: 20
			},
			c),
			d = function() {
				function r() {
					b.zindex++,
					a(".editBox").removeClass("current"),
					a(".editBox ol").css("display", "none"),
					d.find("ol").css("display", "block"),
					n.css("height", d.css("height")),
					d.css({
						"z-index": b.zindex
					}),
					d.addClass("current"),
					b.isFocusbox.length = 0,
					b.isFocusbox.push(d),
					x()
				}
				var d = a("<div class='editBox'></div>");
				g.append(d),
				d.css({
					height: c.height,
					width: c.width,
					left: c.left,
					top: c.top
				}),
				d.html("<ol class='tl'></ol><ol class='tr'></ol><ol class='bl'></ol><ol class='br'></ol><ol class='t' show='yes'></ol><ol class='l' show='yes'></ol><ol class='b' show='yes'></ol><ol class='r' show='yes'></ol><textarea id='textarea'></textarea><ul></ul>");
				var e = d.find("ul"),
				h = (d.find(".tl"), d.find(".tr")),
				i = d.find(".bl"),
				j = d.find(".br"),
				k = d.find(".b"),
				n = d.find(".r"),
				o = d.find("#textarea");
				e.css("border-color", colorset(b.color, "bd")),
				d.find("ol").css({
					"border-color": colorset(b.color, "bd"),
					background: colorset(b.color, "bg")
				}),
				e.html(c.text),
				o.val(c.text),
				o.css({
					height: c.height - 8,
					width: c.width - 8,
					resize: "none"
				}),
				e.css({
					height: c.height - 8,
					width: c.width - 8
				}),
				h.css({
					left: c.width
				}),
				i.css({
					top: c.height
				}),
				j.css({
					left: c.width,
					top: c.height
				}),
				k.css({
					top: c.height,
					display: "block"
				}),
				n.css({
					left: c.width,
					display: "block"
				});
				var p,
				q;
				r(),
				e.bind({
					dblclick: function() {
						o.val(e.text()).css("display", "block").select().focus(),
						e.css("display", "none").text(""),
						b.isBoxEdit = !0,
						c.isEdit = !0
					}
				}),
				o.bind({
					blur: function() {
						e.css("display", "block").text(o.val()),
						o.css("display", "none"),
						b.isBoxEdit = !1,
						c.isEdit = !1
					},
					click: function() {
						return ! 1
					}
				}),
				e.mousedown(function(e) {
					if (!c.isEdit) {
						var f = g.find("#editBox_SelectBox"),
						h = !1;
						d.attr("id", "yes");
						for (var i = 0; i < b.isFocusbox.length; i++) b.isFocusbox[i].attr("id") == d.attr("id") && (h = !0);
						return d.removeAttr("id"),
						h ? f.length > 0 ? (p = e.pageX - parseInt(f.css("left")) - b.offsetX, q = e.pageY - parseInt(f.css("top")) - b.offsetY) : (p = e.pageX - parseInt(d.css("left")) - b.offsetX, q = e.pageY - parseInt(d.css("top")) - b.offsetY) : (p = e.pageX - parseInt(d.css("left")) - b.offsetX, q = e.pageY - parseInt(d.css("top")) - b.offsetY, z(), r()),
						b.isAddbox = !1,
						l.removeClass("current"),
						m.addClass("current"),
						g.css("cursor", "auto"),
						c.isMove = !0,
						x(),
						a(".editBox textarea").blur(),
						!1
					}
				}),
				d.find("ol").mousedown(function(d) {
					return z(),
					r(),
					c.isResize = !0,
					b._x = d.pageX - b.offsetX,
					b._y = d.pageY - b.offsetY,
					c.resizeType = a(this).attr("class"),
					!1
				}),
				a(document).live("mousemove",
				function(a) {
					if (c.isMove) {
						for (var f = g.find("#editBox_SelectBox"), l = 0; l < b.isFocusbox.length; l++) {
							var m,
							r;
							if (f.length > 0) {
								m = parseInt(f.css("width")),
								r = parseInt(f.css("height"));
								var w = a.pageX - p - b.offsetX,
								y = a.pageY - q - b.offsetY;
								b.maxX = parseInt(g.css("width")) - m,
								b.maxY = parseInt(g.css("height")) - r,
								w = 0 > w ? 0: w,
								y = 0 > y ? 0: y,
								w = w > b.maxX ? b.maxX: w,
								y = y > b.maxY ? b.maxY: y,
								f.css({
									top: y,
									left: w
								})
							} else {
								m = parseInt(b.isFocusbox[0].css("width")),
								r = parseInt(b.isFocusbox[0].css("height"));
								var w = a.pageX - p - b.offsetX,
								y = a.pageY - q - b.offsetY;
								b.maxX = parseInt(g.css("width")) - m,
								b.maxY = parseInt(g.css("height")) - r,
								w = 0 > w ? 0: w,
								y = 0 > y ? 0: y,
								w = w > b.maxX ? b.maxX: w,
								y = y > b.maxY ? b.maxY: y,
								b.isFocusbox[0].css({
									top: y,
									left: w
								})
							}
						}
						return x(),
						!1
					}
					if (c.isResize) {
						x();
						var w,
						y,
						z,
						A;
						b.maxX = parseInt(d.css("left")) + parseInt(d.css("width")) - c.minWidth,
						b.maxY = parseInt(d.css("top")) + parseInt(d.css("height")) - c.minHeight,
						b.maxWidth = parseInt(g.css("width")) - parseInt(d.css("left")),
						b.maxHeight = parseInt(g.css("height")) - parseInt(d.css("top"));
						var B = a.pageX - b.offsetX,
						C = a.pageY - b.offsetY;
						switch (B = 0 > B ? 0: B, C = 0 > C ? 0: C, c.resizeType) {
						case "t":
							w = parseInt(d.css("left")),
							y = C,
							z = parseInt(d.css("width")),
							A = parseInt(d.css("height")) + (parseInt(d.css("top")) - C);
							break;
						case "b":
							w = parseInt(d.css("left")),
							y = parseInt(d.css("top")),
							z = parseInt(d.css("width")),
							A = C - parseInt(d.css("top"));
							break;
						case "l":
							w = B,
							y = parseInt(d.css("top")),
							z = parseInt(d.css("width")) + (parseInt(d.css("left")) - B),
							A = parseInt(d.css("height"));
							break;
						case "r":
							w = parseInt(d.css("left")),
							y = parseInt(d.css("top")),
							z = B - parseInt(d.css("left")),
							A = parseInt(d.css("height"));
							break;
						case "tr":
							w = parseInt(d.css("left")),
							y = C,
							z = B - parseInt(d.css("left")),
							A = parseInt(d.css("height")) + (parseInt(d.css("top")) - C);
							break;
						case "tl":
							w = B,
							y = C,
							z = parseInt(d.css("width")) + (parseInt(d.css("left")) - B),
							A = parseInt(d.css("height")) + (parseInt(d.css("top")) - C);
							break;
						case "br":
							w = parseInt(d.css("left")),
							y = parseInt(d.css("top")),
							z = B - parseInt(d.css("left")),
							A = C - parseInt(d.css("top"));
							break;
						case "bl":
							w = B,
							y = parseInt(d.css("top")),
							z = parseInt(d.css("width")) + (parseInt(d.css("left")) - B),
							A = C - parseInt(d.css("top"))
						}
						return z = z < c.minWidth ? c.minWidth: z,
						z = z > b.maxWidth ? b.maxWidth: z,
						A = A < c.minHeight ? c.minHeight: A,
						A = A > b.maxHeight ? b.maxHeight: A,
						w = w > b.maxX ? b.maxX: w,
						w = 0 > w ? 0: w,
						y = y > b.maxY ? b.maxY: y,
						y = 0 > y ? 0: y,
						o.css({
							height: A - 8,
							width: z - 8
						}),
						e.css({
							height: A - 8,
							width: z - 8
						}),
						d.css({
							width: z,
							height: A,
							top: y,
							left: w
						}),
						h.css({
							left: z
						}),
						i.css({
							top: A
						}),
						j.css({
							left: z,
							top: A
						}),
						n.css({
							left: z,
							height: A
						}),
						k.css({
							top: A
						}),
						!1
					}
				}),
				a(document).live("mouseup",
				function() {
					c.isMove = !1,
					c.isResize = !1
				})
			};
			return d()
		};
		var x = function() {
			var c = g.find("#editBox_SelectBox"),
			d = c.length > 0 ? c: b.isFocusbox[0];
			if (null == d) return ! 1;
			a("#panelX").val(parseInt(d.css("left"))),
			a("#panelY").val(parseInt(d.css("top"))),
			a("#panelWidth").val(parseInt(d.css("width"))),
			a("#panelHeight").val(parseInt(d.css("height")));
			var e = d.find("ul").css("font-size"),
			f = d.find("ul").text();
			f = 0 == f.length ? "\u81ea\u5b9a\u4e49": f,
			_exist = a.inArray(f, b.textoption),
			r.find("ol li").removeClass("current"),
			r.find("span").text(_exist > 0 ? f: "\u81ea\u5b9a\u4e49"),
			r.find("ol li:contains('" + f + "')").addClass("current"),
			q.find("ol li").removeClass("current"),
			q.find("span").text(d.find("ul").css("font-size")),
			q.find("ol li:contains('" + e + "')").addClass("current")
		},
		y = function() {
			if (1 == b.isFocusbox.length) {
				_box = b.isFocusbox[0];
				var c = parseInt(a("#panelX").val()),
				d = parseInt(a("#panelY").val()),
				e = parseInt(a("#panelWidth").val()),
				f = parseInt(a("#panelHeight").val());
				b.maxX = parseInt(g.css("width")) - parseInt(b.isFocusbox[0].css("width")),
				b.maxY = parseInt(g.css("height")) - parseInt(b.isFocusbox[0].css("height")),
				b.maxWidth = parseInt(g.css("width")) - parseInt(b.isFocusbox[0].css("left")),
				b.maxHeight = parseInt(g.css("height")) - parseInt(b.isFocusbox[0].css("top")),
				c = c > b.maxX ? b.maxX: c,
				d = d > b.maxY ? b.maxY: d,
				e = e > b.maxWidth ? b.maxWidth: e,
				f = f > b.maxHeight ? b.maxHeight: f,
				a("#panelX").val(c),
				a("#panelY").val(d),
				a("#panelWidth").val(e),
				a("#panelHeight").val(f),
				b.isFocusbox[0].css({
					left: c,
					top: d,
					width: e,
					height: f
				}),
				b.isFocusbox[0].find("textarea").css({
					height: f - 12,
					width: e - 12
				}),
				b.isFocusbox[0].find("ul").css({
					height: f - 8,
					width: e - 8
				}),
				b.isFocusbox[0].find(".tr").css({
					left: e
				}),
				b.isFocusbox[0].find(".bl").css({
					top: f
				}),
				b.isFocusbox[0].find(".br").css({
					left: e,
					top: f
				}),
				b.isFocusbox[0].find(".r").css({
					left: e,
					height: f
				}),
				b.isFocusbox[0].find(".b").css({
					top: f,
					width: e
				}),
				"\u81ea\u5b9a\u4e49" != r.find("span").text() && (b.isFocusbox[0].find("ul").text(r.find("span").text()), b.isFocusbox[0].find("textarea").val(r.find("span").text())),
				b.isFocusbox[0].find("ul").css("font-size", q.find("span").text())
			}
		};
		if (d.find("input").keyup(function() {
			y()
		}), d.find("input").ready(function() {
			a("this").keypress(function() {
				alert(e.keyCode)
			})
		}), s.ready(function() {
			var c = s.find("ol");
			c.attr("class", parseInt(c.css("height"))),
			c.find("li").each(function() {
				a(this).bind("click",
				function() {
					return h.css("background", "url(" + b.imgurl + a(this).attr("id") + ") no-repeat 20px 20px"),
					s.find("span").text(a(this).text()),
					c.find("li").removeClass("current"),
					a(this).addClass("current"),
					b.background = a(this).attr("id"),
					!1
				})
			}),
			s.click(function() {
				b._x = parseInt(c.attr("class")),
				s.css("z-index", 10),
				c.css({
					height: 0,
					display: "block"
				}),
				c.animate({
					height: b._x
				},
				300,
				function() {})
			}),
			s.mouseleave(function() {
				c.animate({
					height: 0
				},
				300,
				function() {
					c.css({
						height: b._x,
						display: "none"
					}),
					s.css("z-index", 5)
				})
			})
		}), t.ready(function() {
			var c = t.find("ol");
			t.find("a").css({
				"border-color": colorset(b.color, "bd"),
				background: colorset(b.color, "bg")
			}),
			t.find("#" + b.color).attr("class", "current"),
			c.attr("class", parseInt(c.css("height"))),
			c.find("li").each(function() {
				a(this).find("b").css({
					"border-color": colorset(a(this).attr("id"), "bd"),
					background: colorset(a(this).attr("id"), "bg")
				}),
				a(this).bind("click",
				function() {
					b.color = a(this).attr("id");
					var d = a(".editBox").find("ul"),
					e = a(".editBox").find("ol");
					return d.css("border-color", colorset(b.color, "bd")),
					e.css({
						"border-color": colorset(b.color, "bd"),
						background: colorset(b.color, "bg")
					}),
					t.find("a").css({
						"border-color": colorset(b.color, "bd"),
						background: colorset(b.color, "bg")
					}),
					c.find("li").removeClass(),
					a(this).attr("class", "current"),
					!1
				})
			}),
			t.click(function() {
				b._x = parseInt(c.attr("class")),
				t.css("z-index", 10),
				c.css({
					height: 0,
					display: "block"
				}),
				c.animate({
					height: b._x
				},
				300,
				function() {})
			}),
			t.mouseleave(function() {
				c.animate({
					height: 0
				},
				300,
				function() {
					c.css({
						height: b._x,
						display: "none"
					}),
					t.css("z-index", 5)
				})
			})
		}), q.ready(function() {
			var c = q.find("ol");
			c.attr("class", parseInt(c.css("height"))),
			c.find("li").each(function() {
				a(this).bind("click",
				function() {
					return c.find("li").removeClass(),
					q.find("span").text(a(this).text()),
					y(),
					a(this).attr("class", "current"),
					!1
				})
			}),
			q.click(function() {
				b._x = parseInt(c.attr("class")),
				q.css("z-index", 10),
				c.css({
					height: 0,
					display: "block"
				}),
				c.animate({
					height: b._x
				},
				300,
				function() {})
			}),
			q.mouseleave(function() {
				c.animate({
					height: 0
				},
				300,
				function() {
					c.css({
						height: b._x,
						display: "none"
					}),
					q.css("z-index", 5)
				})
			})
		}), r.ready(function() {
			var c = r.find("ol");
			c.attr("class", parseInt(c.css("height"))),
			c.find("li").each(function() {
				a(this).bind("click",
				function() {
					return c.find("li").removeClass(),
					r.find("span").text(a(this).text()),
					y(),
					a(this).attr("class", "current"),
					!1
				})
			}),
			r.click(function() {
				b._x = parseInt(c.attr("class")),
				r.css("z-index", 10),
				c.css({
					height: 0,
					display: "block"
				}),
				c.animate({
					height: b._x
				},
				300,
				function() {})
			}),
			r.mouseleave(function() {
				c.animate({
					height: 0
				},
				300,
				function() {
					c.css({
						height: b._x,
						display: "none"
					}),
					r.css("z-index", 5)
				})
			})
		}), u.ready(function() {
			var c = u.find("ol"),
			d = u.find("dl");
			c.attr("class", parseInt(c.css("height"))),
			c.find("li").each(function() {
				a(this).bind("click",
				function() {
					return ! 1
				})
			}),
			u.click(function() {
				u.css("z-index", 10),
				d.css("display", "block"),
				b._x = parseInt(c.attr("class")),
				c.css({
					height: 0,
					display: "block"
				}),
				c.animate({
					height: b._x
				},
				300,
				function() {})
			}),
			u.mouseleave(function() {
				c.animate({
					height: 0
				},
				300,
				function() {
					c.css({
						height: b._x,
						display: "none"
					}),
					d.css("display", "none"),
					u.css("z-index", 5)
				})
			})
		}), l.click(function() {
			b.isFocusbox.length = 0,
			a(".editBox").removeClass("current"),
			a(".editBox ol").css("display", "none"),
			a(this).addClass("current"),
			m.removeClass("current"),
			a("#editCanvas").css("cursor", "crosshair"),
			b.isAddbox = !0
		}), m.click(function() {
			b.isFocusbox.length = 0,
			a(".editBox").removeClass("current"),
			a(".editBox ol").css("display", "none"),
			a(this).addClass("current"),
			l.removeClass("current"),
			a("#editCanvas").css("cursor", "default"),
			b.isAddbox = !1
		}), n.click(function() {
			if (b.isFocusbox.length > 0) {
				for (var a = 0; a < b.isFocusbox.length; a++) b.isFocusbox[a].remove();
				b.isFocusbox.length = 0
			}
			g.find("#editBox_SelectBox").remove()
		}), o.ready(function() {
			var c = o.find("ol");
			c.attr("class", parseInt(c.css("height"))),
			c.find("li").each(function() {
				a(this).bind("click",
				function() {
					var b = a(this).attr("id"),
					c = g.find("#editBox_SelectBox");
					if (c.length > 0) switch (b) {
					case "left":
						c.find(".editBox").css("left", 0);
						break;
					case "right":
						var d = c.find(".editBox");
						d.each(function() {
							a(this).css("left", parseInt(c.css("width")) - parseInt(a(this).css("width")))
						});
						break;
					case "x-center":
						var d = c.find(".editBox");
						d.each(function() {
							a(this).css("left", (parseInt(c.css("width")) - parseInt(a(this).css("width"))) / 2)
						});
						break;
					case "top":
						c.find(".editBox").css("top", 0);
						break;
					case "bottom":
						var d = c.find(".editBox");
						d.each(function() {
							a(this).css("top", parseInt(c.css("height")) - parseInt(a(this).css("height")))
						});
						break;
					case "y-center":
						var d = c.find(".editBox");
						d.each(function() {
							a(this).css("top", (parseInt(c.css("height")) - parseInt(a(this).css("height"))) / 2)
						})
					} else alert("\u81f3\u5c11\u5f97\u9009\u62e9\u4e24\u4e2a\u6216\u4e24\u4e2a\u4ee5\u4e0a\u63a7\u4ef6\uff01");
					return ! 1
				})
			}),
			o.click(function() {

				o.css("z-index", 10),
				b._x = parseInt(c.attr("class")),
				c.css({
					height: 0,
					display: "block"
				}),
				c.animate({
					height: b._x
				},
				300,
				function() {})
			}),
			o.mouseleave(function() {
				c.animate({
					height: 0
				},
				300,
				function() {
					c.css({
						height: b._x,
						display: "none"
					}),
					o.css("z-index", 5)
				})
			})
		}), g.bind("mousedown",
		function(c) {
			return b.isBoxEdit ? void 0: (a(".editBox textarea").blur(), b.isFocusbox.length = 0, a(".editBox").removeClass("current"), a(".editBox ol").css("display", "none"), b.isAddbox ? (g.append("<div class='editBox' id='temp'></div>"), b._x = c.pageX - g.offset().left, b._y = c.pageY - g.offset().top, v = g.find("div:last"), v.css({
				left: b._x,
				top: b._y
			}), !1) : (z(), g.append("<div id='editBox_SelectBox'></div>"), b._x = c.pageX - g.offset().left, b._y = c.pageY - g.offset().top, b.selectbox = g.find("#editBox_SelectBox"), b.selectbox.css({
				left: b._x,
				top: b._y
			}), !1))
		}), g.bind("mousemove",
		function(a) {
			return b.isAddbox && null != v ? (_w = a.pageX - b._x - g.offset().left, _h = a.pageY - b._y - g.offset().top, v.css({
				width: _w,
				height: _h
			}), !1) : b.selectbox ? (_w = a.pageX - b._x - g.offset().left, _h = a.pageY - b._y - g.offset().top, b.selectbox.css({
				width: _w,
				height: _h
			}), !1) : void 0
		}), f.find("ul:first").bind("mousedown",
		function() {
			return b.resize = "b",
			!1
		}), f.find("ul:last").bind("mousedown",
		function() {
			return b.resize = "br",
			!1
		}), a(document).bind({
			mousemove: function(c) {
				if (0 != b.resize && 1 == b.isResize) {
					for (var e = c.pageX - parseInt(g.offset().left), i = c.pageY - parseInt(g.offset().top), j = a(".editBox"), l = 0; l < j.length; l++) b._x = parseInt(j.eq(l).css("left")) + parseInt(j.eq(l).css("width")),
					b._y = parseInt(j.eq(l).css("top")) + parseInt(j.eq(l).css("height")),
					b.minHeight = b.minHeight < b._y ? b._y: b.minHeight,
					b.minWidth = b.minWidth < b._x ? b._x: b.minWidth;
					switch (e = e < b.minWidth ? b.minWidth: e, i = i < b.minHeight ? b.minHeight: i, b.resize) {
					case "b":
						h.css("height", i),
						g.css("height", i - 20),
						k.css({
							height: i - 20
						});
						break;
					case "br":
						h.css({
							width:
							e,
							height: i
						}),
						g.css({
							width: e - 18,
							height: i - 20
						}),
						k.css({
							height: i - 20
						}),
						d.css("width", e),
						f.css("width", e)
					}
					return ! 1
				}
			},
			mouseup: function() {
				if (b.isAddbox && null != v && (v.css("opacity", .5), g.addBox({
					width: parseInt(v.css("width")),
					height: parseInt(v.css("height")),
					left: parseInt(v.css("left")),
					top: parseInt(v.css("top"))
				}), v.remove(), v = null, g.find("#temp").remove()), 0 != b.selectbox) {
					for (var d = parseInt(b.selectbox.css("left")), e = parseInt(b.selectbox.css("width")) + parseInt(b.selectbox.css("left")), f = parseInt(b.selectbox.css("top")), h = parseInt(b.selectbox.css("height")) + parseInt(b.selectbox.css("top")), i = g.find(".editBox"), j = e, k = h, l = d, m = f, n = 0; n < i.length; n++) {
						var o = parseInt(i.eq(n).css("left")),
						p = parseInt(i.eq(n).css("top")),
						q = parseInt(i.eq(n).css("left")) + parseInt(i.eq(n).css("width")),
						r = parseInt(i.eq(n).css("top")) + parseInt(i.eq(n).css("height"));
						o >= d && p >= f && e >= q && h >= r && (j = j > o ? o: j, k = k > p ? p: k, l = q > l ? q: l, m = r > m ? r: m, b.isFocusbox.push(i.eq(n)), i.eq(n).find("ol").css("display", "block"), i.eq(n).find("ul").addClass("current"))
					}
					if (b.selectbox.css({
						left: j,
						top: k,
						width: l - j,
						height: m - k
					}), b.isFocusbox.length > 0) for (var n = 0; n < b.isFocusbox.length; n++) b.isFocusbox[n].prependTo(b.selectbox),
					b.isFocusbox[n].css({
						left: parseInt(b.isFocusbox[n].css("left")) - parseInt(b.selectbox.css("left")),
						top: parseInt(b.isFocusbox[n].css("top")) - parseInt(b.selectbox.css("top"))
					});
					else a("#editBox_SelectBox").remove();
					x(),
					b.selectbox = !1
				}
				b.resize = !1
			},
			keydown: function(a) {
				var c = g.find("#editBox_SelectBox"),
				d = a.keyCode;
				if (c.length > 0) {
					var e = parseInt(c.css("left")),
					f = parseInt(c.css("top"));
					b.maxX = parseInt(g.css("width")) - parseInt(c.css("width")),
					b.maxY = parseInt(g.css("height")) - parseInt(c.css("height"));
					var h,
					j,
					k,
					l;
					switch (h = e > 0 ? e - 1: 0, j = e < b.maxX ? e + 1: b.maxX, k = f > 0 ? f - 1: 0, l = f < b.maxY ? f + 1: b.maxY, d) {
					case 46:
						c.remove();
						break;
					case 37:
						c.css("left", h),
						x();
						break;
					case 39:
						c.css("left", j),
						x();
						break;
					case 38:
						c.css("top", k),
						x();
						break;
					case 40:
						c.css("top", l),
						x()
					}
				} else if (b.isFocusbox.length > 0) for (i = 0; i < b.isFocusbox.length; i++) {
					var e = parseInt(b.isFocusbox[i].css("left")),
					f = parseInt(b.isFocusbox[i].css("top"));
					b.maxX = parseInt(g.css("width")) - parseInt(b.isFocusbox[i].css("width")),
					b.maxY = parseInt(g.css("height")) - parseInt(b.isFocusbox[i].css("height"));
					var h,
					j,
					k,
					l;
					switch (h = e > 0 ? e - 1: 0, j = e < b.maxX ? e + 1: b.maxX, k = f > 0 ? f - 1: 0, l = f < b.maxY ? f + 1: b.maxY, a.keyCode) {
					case 46:
						b.isFocusbox[i].remove();
						break;
					case 37:
						b.isFocusbox[i].css("left", h),
						x();
						break;
					case 39:
						b.isFocusbox[i].css("left", j),
						x();
						break;
					case 38:
						b.isFocusbox[i].css("top", k),
						x();
						break;
					case 40:
						b.isFocusbox[i].css("top", l),
						x()
					}
				}
				b.resize = !1
			}
		}), colorset = function(a, b) {
			switch (a) {
			case "red":
				return "bd" == b ? "#D00": "#FA7366";
			case "yellow":
				return "bd" == b ? "#333": "#F90";
			case "green":
				return "bd" == b ? "#016801": "#008C00";
			case "blue":
				return "bd" == b ? "#005EC7": "#82B7F1";
			case "purple":
				return "bd" == b ? "#AE037B": "#DA90C8";
			case "black":
				return "bd" == b ? "#000": "#555"
			}
		},
		a.browser.msie) {
			var A = parseInt(d.css("width"));
			d.css("width", A)
		}
		p.click(function() {
			z();
			for (var e, f, c = g.find(".editBox"), d = [], h = 0; h < c.length; h++) e = c.eq(h),
			d.push('{"width":' + parseInt(e.css("width")) + ',"height":' + parseInt(e.css("height")) + ',"left":' + parseInt(e.css("left")) + ',"top":' + parseInt(e.css("top")) + ',"txt":"' + e.find("ul").text() + '"}');
			f = '{"width":' + b.width + ',"height":' + b.height + ',"background":"' + b.background + '","list":[' + d.join(",") + "]}";
			var i = new Date;
			i.setDate(i.getDate() + 365),
			a.cookie("editBox_data", d.join(",\n"), i),
			alert(f)
		})
	}
} (jQuery);