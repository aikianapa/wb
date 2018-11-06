$(document).on("wb-delegates",function(){
	$("[data-wb-role=cart]").each(function(){
		if ($(this).data("wb_cart")==undefined) {
			$(this).wbCart();
		}
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
			$.get(ajax, form, function(data) {
				$(that).trigger("add-to-cart-done", [getcookie("order_id")]);
				if ($("[data-wb-role=cart][data-wb-tpl]").length) {
					wb_setdata("[data-wb-role=cart][data-wb-tpl]", {});
				}
				$cart.trigger("cart-total-recalc");
				$cart.trigger("cart-add-done", data);
			});
			return false;
		});
		// Cart clear
		$cart.on("cart-clear",function(e,that){
			var ajax = "/ajax/cart/cart-clear";
			$.get(ajax, function(data) {
				$("[data-wb-role=cart] .cart-item").remove();
				$(document).trigger("cart-total-recalc");
				$(document).trigger("cart-after-clear", [event]);
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
			$cart.find(".cart-item").each(function(i) {
				$(this).attr("idx", i);
			});
			$cart.trigger("cart-total-recalc");
			$cart.trigger("cart-item-remove-done");
		});
		// Cart item recalc
		$cart.on("cart-item-recalc", function(event, item) {
			var total = 0;
			var arr = wb_cart_item(item);
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
			$cart.find(".cart-count").text(count);
			$cart.find(".cart-total").text(total);
			$cart.find(".cart-lines").text(lines);
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
				form[i] = wb_cart_item(this);
			});
			if (ajax == undefined || ajax == "") {
				var ajax = "/ajax/cart/cart-update";
			}
			var diff = $.post(ajax, form);
			$cart.trigger("cart-update-done");
		});


	}

	$cart.delegate("input:not(.cart-item-quant),select,textarea","change", function() {
		var item = $(this).parents(".cart-item");
		$cart.trigger("cart-item-recalc", item);
		$cart.trigger("cart-total-recalc");
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



function wb_cart() {

  $(document).unbind("cart-recalc");
  $(document).unbind("cart-clear");
  $(document).unbind("cart-item-recalc");
  $(document).unbind("cart-item-plus");
  $(document).unbind("cart-item-minus");
  $(document).unbind("cart-item-remove");
  $(document).unbind("cart-total-recalc");
  $("[data-wb-role=cart]").find("input,select,textarea").unbind("change");
  $("[data-wb-role=cart] .cart-item").find("*").unbind("click");
  $("[data-wb-role=cart] .cart-clear").unbind("click");
  $("[data-wb-role=cart] .add-to-art").unbind("click");
  $(document).undelegate("form[data-wb-role=cart] .add-to-cart", "click");


  $(document).undelegate(".cart-clear", "click");
  $(document).delegate(".cart-clear", "click", function() {
    $(this).trigger("cart-clear", [this]);
    return false;
  });
  $(document).off("cart-recalc");
  $(document).on("cart-recalc", function(event, flag) {
    $("[data-wb-role=cart] .cart-item").each(function() {
      $(this).trigger("cart-item-recalc", [this, flag]);
    });
    $(document).trigger("cart-total-recalc");
  });


  $(document).off("cart-total-recalc");
  $(document).on("cart-total-recalc", function(event, item, flag) {
    $("[data-wb-role=cart]:not(form)").each(function() {
      var total = 0;
      var lines = 0;
      var count = 0;
      $(this).find(".cart-item").each(function() {
        $(document).trigger("cart-item-recalc", $(this));
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
      $(this).find(".cart-count").text(count);
      $(this).find(".cart-total").text(total);
      $(this).find(".cart-lines").text(lines);
      if ($(this).attr("data-wb-writable") == "true") {
        $(document).trigger("cart-update");
      }
    });
    $(document).trigger("cart-recalc-done");
  });
  $(document).off("cart-update");
  $(document).on("cart-update", function(event) {
    console.log("cart-update");
    var cart = $(document).find("[data-wb-role=cart][data-wb-writable=true]:first");
    var ajax = $(cart).attr("data-wb-ajax");
    var form = {};
    $(cart).find(".cart-item").each(function(i) {
      form[i] = wb_cart_item(this);

    });
    if (ajax == undefined || ajax == "") {
      var ajax = "/ajax/cart/cart-update";
    }
    var diff = $.post(ajax, form);
    $(document).trigger("cart-update-done");
  });

  $("[data-wb-role=cart]").find("input,select,textarea").off("change");
  $("[data-wb-role=cart]").find("input,select,textarea").on("change", function() {
    var item = $(this).parents(".cart-item");
    $(document).trigger("cart-item-recalc", item);
    $(document).trigger("cart-total-recalc");
  });
  $(document).undelegate("[data-wb-role=cart] .cart-item *", "click");
  $(document).delegate("[data-wb-role=cart] .cart-item *", "click", function() {
    var item = $(this).parents(".cart-item");
    if ($(this).hasClass("cart-item-remove")) {
      $(document).trigger("cart-item-remove", item);
    }
    if ($(this).hasClass("cart-item-plus")) {
      $(document).trigger("cart-item-plus", item);
    }
    if ($(this).hasClass("cart-item-minus")) {
      $(document).trigger("cart-item-minus", item);
    }
  });
  $(document).trigger("cart-recalc", ["noajax"]);
  wbapp.cart = wb_cart_get();
};

function wb_cart_item(item) {
  var arr = {};
  var fld = new Array("id", "form", "count", "price");
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
    arr[$(this).attr("name")] = $(this).val();
  });
  return arr;
}

function wb_cart_get() {
  var cart = null;
  var defer = $.ajax({
    async: false,
    type: 'GET',
    url: "/ajax/cart/getdata",
    success: function(data) {
      cart = $.parseJSON(data);
      return cart;
    }
  });
  return cart;
}

$(document).trigger("wb-delegates");
