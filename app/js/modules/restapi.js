import { genPassword } from "../helpers.js";

export function initRestApi() {
    // console.info("ElastPro restapi module initialized");

    $(document).on("click", "#gen_apikey", function(e) {
        $('#txtapikey').val(genPassword(32).toLowerCase());
    });
}