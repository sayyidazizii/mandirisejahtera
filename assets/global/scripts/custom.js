$(document).ready(function(){
    let  button_to_down = $('.scroll-to-down');
    let offset = document.body.offsetHeight - window.innerHeight - 300;
    let duration = 500;
    console.log();
    if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {  // ios supported
        $(window).bind("touchend touchcancel touchleave", function(e){
           if ($(this).scrollTop() < offset) {
                $('.scroll-to-down').fadeIn(duration);
            } else {
                $('.scroll-to-down').fadeOut(duration);
            }
        });
    } else {  // general 
        $(window).scroll(function() {
            if ($(this).scrollTop() < offset) {
                $('.scroll-to-down').fadeIn(duration);
            } else {
                $('.scroll-to-down').fadeOut(duration);

            }
        });
    }
    

    
     button_to_down.on('click', function(){
        // alert('hai');
        window.scrollTo({ left: 0, top: document.body.scrollHeight, behavior: "smooth" });
    });
})