var priceOptions = {"50_websites":{"1_year":{"price":79,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=3"},"2_year":{"price":125,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=15"},"lifetime":{"price":199,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=9"}},"500_websites":{"1_year":{"price":139,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=16"},"2_year":{"price":225,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=17"},"lifetime":{"price":359,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=18"}},"1000_websites":{"1_year":{"price":199,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=19"},"2_year":{"price":315,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=20"},"lifetime":{"price":499,"link":"https:\/\/go.premio.io\/?edd_action=add_to_cart&download_id=2199&edd_options[price_id]=21"}}};
jQuery(document).ready(function($){
    jQuery('.my-color-field').wpColorPicker();
    jQuery(document).on('click', '.sticky-header-upgrade-now', function(e){
        e.preventDefault();
        jQuery(".sticky-header-menu ul li a:last").trigger("click");
    });

    if(jQuery(".multiple-options").length) {
        jQuery(".multiple-options").select2({
            minimumResultsForSearch: -1
        });
    }
    if(jQuery(".multiple-web-options").length) {
        jQuery(".multiple-web-options").select2({
            minimumResultsForSearch: -1
        });
    }
    jQuery(document).on("change", ".multiple-options", function(){
        priceText = jQuery(this).find("option:selected").attr("data-header");
        thisValue = jQuery(this).val();
        thisPrice = jQuery(this).find("option:selected").attr("data-price");
        if(!jQuery(this).hasClass("has-multiple-websites")) {
            jQuery(this).closest(".price-table").find("a.cart-link").attr("href", thisValue);
            jQuery(this).closest(".price-table").find(".plan-price").text("$" + thisPrice);
        } else {
            var webOption = jQuery(".multiple-web-options").val();
            var priceSettings = priceOptions[webOption];
            var yearPlan = jQuery(".multiple-options.has-multiple-websites option:selected").attr("data-option");
            if(priceSettings[yearPlan] != undefined) {
                priceSettings = priceSettings[yearPlan];
                thisValue = priceSettings.link;
                thisPrice = priceSettings.price;
            }
        }
        thisOption = jQuery(this).find("option:selected").attr("data-option");
        if(thisOption == "1_year") {
            thisPrice = thisPrice+"<span>/year</span>";
            priceText = "Renewals for <b>25% off</b>";
        } else if(thisOption == "2_year") {
            thisPrice = thisPrice+"<span>/2 years</span>";
            priceText = "Renewals for <b>25% off</b>";
        } else {
            thisPrice = thisPrice+"<span>/lifetime</span>";
            priceText = "For lifetime";
        }
        jQuery(this).closest(".price-table").find("a.cart-link").attr("href", thisValue);
        jQuery(this).closest(".price-table").find(".plan-price").html("$" + thisPrice);
        jQuery(this).closest(".price-table").find(".price-offer").html(priceText);
    });

    jQuery(document).on("change", ".multiple-web-options", function(){
        jQuery(".multiple-options.has-multiple-websites").trigger("change");
    });

    if(jQuery(".multiple-options.has-multiple-websites").length) {
        jQuery(".multiple-options.has-multiple-websites").trigger("change");
    }
});