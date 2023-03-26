/**
 * WEBSITE: https://themefisher.com
 * TWITTER: https://twitter.com/themefisher
 * FACEBOOK: https://www.facebook.com/themefisher
 * GITHUB: https://github.com/themefisher/
 */

(function ($) {
    'use strict';
  
    // Preloader
    $(window).on('load', function () {
      $('#preloader').fadeOut('slow', function () {
        $(this).remove();
      });
    });
  
    
    // Instagram Feed
    if (($('#instafeed').length) !== 0) {
      var accessToken = $('#instafeed').attr('data-accessToken');
      var userFeed = new Instafeed({
        get: 'user',
        resolution: 'low_resolution',
        accessToken: accessToken,
        template: '<a href="{{link}}"><img src="{{image}}" alt="instagram-image"></a>'
      });
      userFeed.run();
    }
  
    // setTimeout(function () {
    //   $('.instagram-slider').slick({
    //     dots: false,
    //     speed: 300,
    //     // autoplay: true,
    //     arrows: false,
    //     slidesToShow: 6,
    //     slidesToScroll: 1,
    //     responsive: [{
    //         breakpoint: 1024,
    //         settings: {
    //           slidesToShow: 4
    //         }
    //       },
    //       {
    //         breakpoint: 600,
    //         settings: {
    //           slidesToShow: 3
    //         }
    //       },
    //       {
    //         breakpoint: 480,
    //         settings: {
    //           slidesToShow: 2
    //         }
    //       }
    //     ]
    //   });
    // }, 1500);
  
  
    // e-commerce touchspin
    $('input[name=\'product-quantity\']').TouchSpin();
  
  
    // Video Lightbox
    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
      event.preventDefault();
      $(this).ekkoLightbox();
    });
  
  
    // Count Down JS
    $('#simple-timer').syotimer({
      year: 2022,
      month: 5,
      day: 9,
      hour: 20,
      minute: 30
    });
  
    //Hero Slider
    // $('.hero-slider').slick({
    //   // autoplay: true,
    //   infinite: true,
    //   arrows: true,
    //   prevArrow: '<button type=\'button\' class=\'heroSliderArrow prevArrow tf-ion-chevron-left\'></button>',
    //   nextArrow: '<button type=\'button\' class=\'heroSliderArrow nextArrow tf-ion-chevron-right\'></button>',
    //   dots: true,
    //   autoplaySpeed: 7000,
    //   pauseOnFocus: false,
    //   pauseOnHover: false
    // });
    // $('.hero-slider').slickAnimation();
  
  
  })(jQuery);
  


//   wowlslider
// -----------------------------------------------------------------------------------
// http://wowslider.com/
// JavaScript Wow Slider is a free software that helps you easily generate delicious 
// slideshows with gorgeous transition effects, in a few clicks without writing a single line of code.
// Generated by WOW Slider 9.0
//
//***********************************************
// Obfuscated by Javascript Obfuscator
// http://javascript-source.com
//***********************************************
// jQuery("#wowslider-container1").wowSlider({effect:"basic",prev:"",next:"",duration:34*100,delay:32*100,width:1900,height:600,autoPlay:true,autoPlayVideo:false,playPause:false,stopOnHover:true,loop:false,bullets:1,caption:false,captionEffect:"none",controls:true,controlsThumb:false,responsive:2,fullScreen:false,gestures:2,onBeforeStep:0,images:0});





$("form").submit(function(){
          if (!$('checkbox').prop('checked')) {
              $('checkbox').bootstrapToggle('on')
              $('checkbox').val('off')
          }
  });



  function SendXMLHttpRequest(formData, url, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
	xhr.timeout = 10000;
    xhr.onloadstart = function () {
        //progressbar document.querySelector("#loader").style.display = "flex";
		console.log("onloadstart");
    }
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log("status 200 başarılı");
                  // return this.responseText;
            callback(this.responseText,this.status);
          }
        if (this.status == 201) {
            console.log("status 201 BAŞARILI");
            console.log(this.readyState);
            console.log(this.status);
            console.log(this.responseText);
      			callback(this.responseText,this.status);
        }
    }
    xhr.send(formData);
}

$(document).ready(function(){
  $('.telmask').mask('0(000) 000-00-00',{placeholder:"0(___) ___-__-__"});
  });