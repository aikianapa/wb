$(document).on("wb-delegates",function(){
	$("[data-wb-role=cart]").each(function(){
		if ($(this).data("wb_cart")==undefined) {
			$(this).wbCart();
		}
		console.log("Cart init");
	});
	//wb_cart();
});

$.fn.wbCart = function(param) {
	$cart = $(this);
	if (param==undefined) {
		// Add to cart
		$cart.on("cart-add-click",function(e,that){
			var form = $cart.serialize();
			if ($cart.attr("data-wb-ajax") == undefined || ajax == "") {
				var ajax = "/ajax/cart/add-to-cart";
			} else {
				var ajax = $cart.attr("data-wb-ajax");
			}
			$.post(ajax, form, function(data) {
				$(that).trigger("add-to-cart-done", [getcookie("order_id")]);
				if ($("[data-wb-role=cart][data-wb-tpl]").length) {
					wb_setdata("[data-wb-role=cart][data-wb-tpl]", {});
				}
				$cart.trigger("cart-total-recalc");
				$cart.trigger("cart-add-done", data);
				console.log("trigger: cart-add-done");
				$cart.wbCartMsg(wbapp.sysmsg.cart_add);
			});
			return false;
		});

		// Cart clear
		$cart.on("cart-clear",function(e,that){
			var ajax = "/ajax/cart/cart-clear";
			$.get(ajax, function(data) {
				$("[data-wb-role=cart] .cart-item").remove();
				$("[data-wb-role=cart]").trigger("cart-total-recalc");
				$("[data-wb-role=cart]").trigger("cart-after-clear", [event]);
				$cart.trigger("cart-clear-done", data);
				console.log("trigger: cart-clear-done");
				$cart.wbCartMsg(wbapp.sysmsg.cart_clear);
			});
			return false;
		});
		// Cart minus
		$cart.on("cart-item-minus", function(event, that) {
			var item = $(that).parents(".cart-item");
			var idx = $(item).attr("idx");
			var quant = $(item).find(".cart-item-quant");
			if ($(item).attr("min")==undefined) {var min = 1;} else {var min = $(item).attr("min")*1;}

			if ($(quant).is("input") || $(quant).is("select")) {
				if ($(quant).val() > min) {
					$(quant).val($(quant).val() * 1 - 1);
				}
			} else {
				if ($(quant).text() * 1 > min) {
					$(quant).html($(quant).text() * 1 - 1);
				}
			}

			$cart.trigger("cart-item-recalc", item);
			$cart.trigger("cart-total-recalc");
		});
		// Cart plus
		$cart.on("cart-item-plus", function(event, that) {
			var item = $(that).parents(".cart-item");
			var quant = $(item).find(".cart-item-quant");
			if ($(item).attr("max")==undefined) {var max = 999999;} else {var max = $(item).attr("max")*1;}
			if ($(quant).is("input") || $(quant).is("select")) {
				if ($(quant).val() < max) {
					$(quant).val($(quant).val() * 1 + 1);
				}
			} else {
				if ($(quant).text() * 1 < max) {
					$(quant).html($(quant).text() * 1 + 1);
				}
			}

			$cart.trigger("cart-item-recalc", item);
			$cart.trigger("cart-total-recalc");
		});
		// Cart remove
		$cart.on("cart-item-remove", function(event, that) {
			$(that).parents(".cart-item").remove();
			$("[data-wb-role=cart]").find(".cart-item").each(function(i) {
				$(this).attr("idx", i);
			});
			$("[data-wb-role=cart]").trigger("cart-total-recalc");
			$("[data-wb-role=cart]").trigger("cart-item-remove-done");
			$cart.wbCartMsg(wbapp.sysmsg.cart_remove);
		});
		// Cart item recalc
		$cart.on("cart-item-recalc", function(event, item) {
			var total = 0;
			var arr = $cart.wbCartItem(item);
			if (arr.price !== undefined && arr.quant !== undefined) {
				total = arr.price * arr.quant;
			}
			if ($(item).find(".cart-item-total").is(":input")) {
				$(item).find(".cart-item-total").val(total);
			} else {
				$(item).find(".cart-item-total").html(total);
			}
			$cart.on("cart-item-recalc-done");
		});
		// Cart total recalc
		$cart.on("cart-total-recalc", function(event) {
			if ($cart.find(".cart-item").length) {
				var total = 0;
				var lines = 0;
				var count = 0;
				$cart.find(".cart-item").each(function() {
					$cart.trigger("cart-item-recalc", $(this));
					if ($(this).find(".cart-item-total").is(":input")) {
					total = total + $(this).find(".cart-item-total").val() * 1;
					} else {
					total = total + $(this).find(".cart-item-total").text() * 1;
					}
					if ($(this).find(".cart-item-count").is(":input")) {
					count = count + $(this).find(".cart-item-count").val() * 1;
					} else {
					count = count + $(this).find(".cart-item-count").text() * 1;
					}
					lines++;
				});
				$("[data-wb-role=cart] .cart-count").text(count);
				$("[data-wb-role=cart] .cart-total").text(total);
				$("[data-wb-role=cart] .cart-lines").text(lines);
			} else {
				var ajax = "/ajax/cart/getdata";
				$.post(ajax, {}, function(data) {
					var data = $.parseJSON(data);
					$("[data-wb-role=cart] .cart-count").text(data.count);
					$("[data-wb-role=cart] .cart-total").text(data.total);
					$("[data-wb-role=cart] .cart-lines").text(data.lines);
				});
			}
			if (!$cart.is("form") && $cart.data("wb_cart")!==undefined) {
				$cart.trigger("cart-update");
			}
		});
		// Cart update

		$cart.on("cart-update", function(event) {
			console.log("cart-update");
			var ajax = $cart.attr("data-wb-ajax");
			var form = {};
			$cart.find(".cart-item").each(function(i) {
				form[i] = $cart.wbCartItem(this);
			});
			if (ajax == undefined || ajax == "") {
				var ajax = "/ajax/cart/cart-update";
			}
			var diff = $.post(ajax, form);
			$cart.trigger("cart-update-done");
		});


	}

	$cart.delegate("input,select,textarea","change", function() {
		if ($(this).parents(".cart-table").length) {
		    var item = $(this).parents(".cart-item");
		    $cart.trigger("cart-item-recalc", item);
		    $cart.trigger("cart-total-recalc");
		}
	});

	$cart.delegate("input.cart-item-quant","keyup", function() {
		var item = $(this).parents(".cart-item");
		$cart.trigger("cart-item-recalc", item);
		$cart.trigger("cart-total-recalc");
	});

	$cart.delegate(".add-to-cart", 		"click", function() {$cart.trigger("cart-add-click",	[this]);});
	$cart.delegate(".cart-clear", 		"click", function() {$cart.trigger("cart-clear",	[this]);});
	$cart.delegate(".cart-item-minus", 	"click", function() {$cart.trigger("cart-item-minus",	[this]);});
	$cart.delegate(".cart-item-plus", 	"click", function() {$cart.trigger("cart-item-plus",	[this]);});
	$cart.delegate(".cart-item-remove", 	"click", function() {$cart.trigger("cart-item-remove",	[this]);});
	$cart.trigger("cart-total-recalc");
	$cart.data("wb_cart",true);
}

	$.fn.wbCartMsg = function(data) {
		if ($.bootstrapGrowl && data !== false) {
			$.bootstrapGrowl(data, {
				ele: 'body',
				type: 'success',
				offset: {
					from: 'top',
					amount: 20
				},
				align: 'right',
				width: "auto",
				delay: 4000,
				allow_dismiss: true,
				stackup_spacing: 10
			});
		}
	}


	$.fn.wbCartItem = function(item) {
		var arr = {};
		var fld = new Array("id", "form", "quant", "price");
		var add = $(this).parents("[data-wb-role=cart]").attr("data-wb-update");
		// можно передать список полей, участвующих в пересчёте
		if (add !== undefined && add !== "") {
			fld = fld + explode(",", add);
		}
		for (var i in fld) {
			var fldname = (fld[i]).trim();
			var field = $(item).find(".cart-item-" + fldname);
			if (field.is(":input")) {
				var value = field.val();
			} else {
				var value = field.text();
			}
			if (is_numeric(value * 1)) {
				value = value * 1;
			}
			if (fldname == "id") fldname = "item";
			arr[fldname] = value;
		};
		$(item).find("[name]:input").each(function() {
			if ($(this).attr("data-wb-field")!==undefined) {
				field = $(this).attr("data-wb-field");
			} else {
				field = $(this).attr("name");
			}
			arr[field] = $(this).val();
		});
		return arr;
	}

$(document).on("orders_after_formsave",function(event,name,item,form,res){
//	$("#modalOrder").modal("hide");
//	$("#orderCheckout").show();
//	$("[data-role=cart] .cart-table").hide();
//	$("[data-role=cart] .cart-success").show();
	//$(document).trigger("cart-clear");
	wbapp.merchantModal('show');
});

$(document).trigger("wb-delegates");
