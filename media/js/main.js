$(function() {

    (function getWeather() {
        if ($("main#index").length) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                alert("Geolocation is not supported by this browser.");
            }

            function showPosition(position) {
                var lat = position.coords.latitude,
                    lng = position.coords.longitude;

                $.ajax({
                    url: "/index/getWeather",
                    type: "POST",
                    data: {lat: lat, lng: lng},
                    success: function(data) {
                        data = JSON.parse(data);
                        if (data.success) {
                            data = JSON.parse(data.data);
                            console.log(data);
                        } else {
                            alert("Error occurred while getting weather information");
                        }
                    }
                });
            }

            function showError(error) {
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert("User denied the request for Geolocation.");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert("Location information is unavailable.");
                        break;
                    case error.TIMEOUT:
                        alert("The request to get user location timed out.");
                        break;
                    case error.UNKNOWN_ERROR:
                        alert("An unknown error occurred.");
                        break;
                }
            }
        }
    })();

    (function register() {
        var imgInput = $("#registerForm-image"),
            imageHolder = $(".form-element.imageHolder .input-image");

        imageHolder.on('click', function() {
            imgInput.trigger('click');
        });

        imgInput.on('change', function() {
            var filename = $(this).val().split('\\').pop();
            if(!filename.length) {
                filename = "Add picture";
            }
            imageHolder.find('span').html(filename);
        });

        var form = $("#registerForm");

        form.on('submit', function(e) {
            form.find("input:not([type='file'])").each(function() {
                if (!$(this).val().length) {
                    e.preventDefault();
                    alert('One or more fields are empty');
                    return false;
                }
            });
            if (form.find("input[name='password']").val() !== form.find("input[name='confirm_password']").val()) {
                e.preventDefault();
                alert('Passwords are not equal');
            }
        });
    })();
});