<section id="cookie_section">
    <div id="cookie_directive_container" class="container-fluid cookie-fixed" style="display: none">
        <div class="row text-center mx-auto" id="cookie_accept"> 
            <div class="col pt-1">
                <a href="#" class="btn btn-secondary pull-right br-radius-50">Close</a>
                <p class="text-muted font-white">
                    By using our website you are consenting to use cookies. We use cookies to enhance your experience, NEVER for profiling purposes.
                </p> 
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    jQuery(function ($) {

        checkCookie_eu();

        function checkCookie_eu()
        {

            var consent = getCookie_eu("cookies_consent");

            if (consent == null || consent == "" || consent == undefined)
            {
                // show notification bar
                $('#cookie_directive_container').show();
            }

        }

        function setCookie_eu(c_name, value, exdays)
        {

            var exdate = new Date();
            exdate.setDate(exdate.getDate() + exdays);
            var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
            document.cookie = c_name + "=" + c_value + "; path=/";

            $('#cookie_directive_container').hide('slow');
        }


        function getCookie_eu(c_name)
        {
            var i, x, y, ARRcookies = document.cookie.split(";");
            for (i = 0; i < ARRcookies.length; i++)
            {
                x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
                y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
                x = x.replace(/^\s+|\s+$/g, "");
                if (x == c_name)
                {
                    return unescape(y);
                }
            }
        }

        $("#cookie_accept a").click(function () {
            setCookie_eu("cookies_consent", 1, 30);
        });

    });
</script>
