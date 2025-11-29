$(document).ready(function () {
  var stickyElement = $(".sticky");
  if (!stickyElement.length) {
    return;
  }

  var stickyClass = "sticky-pin",
    stickyPos = stickyElement.offset().top,
    stickyHeight;

  stickyElement.after('<div class="jumps-prevent"></div>');
  function jumpsPrevent() {
    stickyHeight = stickyElement.innerHeight();
    stickyElement.css({ "margin-bottom": "-" + stickyHeight + "px" });
    stickyElement.next().css({ "padding-top": +stickyHeight + "px" });
  }
  jumpsPrevent();

  $(window).resize(function () {
    jumpsPrevent();
  });

  function stickerFn() {
    var winTop = $(this).scrollTop();
    if (winTop >= stickyPos) {
      stickyElement.addClass(stickyClass);
    } else {
      stickyElement.removeClass(stickyClass);
    }
  }
  stickerFn();

  $(window).scroll(function () {
    stickerFn();
  });
});
