jQuery(document).ready(function($){

	$("input[name$='mts_coupon_button_type']").click(function() {
		if($(this).attr("value")=="deal"){
			$("div.cmb2-id-mts-coupon-code").hide();
		}
		if($(this).attr("value")=="coupon"){
			$("div.cmb2-id-mts-coupon-code").show();
		}
	});

	if ($("#mts_coupon_button_type2").is(':checked')) {
	   $("div.cmb2-id-mts-coupon-code").show();
	} else {
	   $("div.cmb2-id-mts-coupon-code").hide();
	}

});