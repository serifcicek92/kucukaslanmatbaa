$(document).ready(function () {

    // Scroll to screen height if previously saved

    var previousHeight = localStorage.getItem("pageHeight");

    if (previousHeight) {

        $('html, body').animate({

            scrollTop: localStorage.getItem("pageHeight")

        }, 1000);

        localStorage.setItem("pageHeight", null);

    }



    // Saves scroll height on submit

    $("form").on("submit", function () {

        localStorage.setItem("pageHeight", $(window).scrollTop());

    });

});



$.getJSON("https://api.ipify.org?format=json", function (data) {



    // Setting text of element P with id gfg

    const xhrip = new XMLHttpRequest();

    xhrip.open("POST", "user/uservisit");

    const formData = new FormData();

    formData.append("visitip", data.ip);

    formData.append("page", window.location.href);

    xhrip.onreadystatechange = function () {/*console.log(this.responseText);*/ }

    xhrip.send(formData);

});