// Helpers
log = console.log;
// AJAX Add to Cart Component
// Add Product to cart by ajax
var root = document.location.hostname;
var getAjaxStoteUrl = function(type) {
  return root === 'store1.shoperti.app' ? 'https://'+ root + '/' + type : 'https://'+ root + '/' + type;
}
var ajaxConfig = {
  getUrl:   getAjaxStoteUrl('cart.json'),
  postUrl: getAjaxStoteUrl('cart/add.js')
}
var formatMoney = function(price) {
  var formatPrice = price /= 100;
  formatPrice = formatPrice.toFixed(2);
  formatPrice = formatPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return '$' + formatPrice + ' {{ store.currency }}';
}
var closeCartModal = function() {
  var _isHover = $('.mini-cart').is(':hover');
  var _isModalHover = $('.mini-cart__content').is(':hover');
  if (!_isModalHover || !_isHover) {
    $('.mini-cart__content').slideUp();
    return false;
  }
  return false;
}

$('.mini-cart__link.mini-cart--is-full').hover(function() {
  var _this = this;
  if ($(window).width() > 667) {
    $('.mini-cart__content').show();
  }
});

$('.mini-cart__content', '.mini-cart').mouseleave(function() {
  setTimeout(function(){
    closeCartModal();
  }, 2000);
});
var updateCartQty = function(qty) {
  $('.mini-cart--qty').text(qty);
}
var buildModalProductCart = function(data) {
  var _sku = data;
  var _newHtmlProduct = '<div class=\"mini-cart-product\">';
  _newHtmlProduct += '<div class=\"mini-cart-image\"><a href=\"/productos/' + _sku.product.permalink + '/'+_sku.sku.permalink +'\"><img src=\"'+ _sku.sku.image_url +'\"></a></div>';
  _newHtmlProduct += '<div class=\"mini-cart-name\"><a href=\"/productos/' + _sku.product.permalink + "/" + _sku.sku.permalink + '\">' + _sku.product.name + '</a></div>';
  _newHtmlProduct += '<div class=\"mini-cart-attributes\"><div class=\"attribute\"><div class=\"size\"><span class=\"label\">Talla:</span><span class=\"value\"> '+ _sku.sku.modifiers[1] +'</span></div></div></div>';
  _newHtmlProduct += '<div class=\"mini-cart-pricing\"><span class=\"label\">Cantidad: </span><span class=\"value\"> '+ _sku.quantity +'</span><span class=\"mini-cart-price\">'+ formatMoney(_sku.price) +'</span></div>';
  _newHtmlProduct += '</div>';

  return _newHtmlProduct;
}
var miniCartTotals = function(total) {
  var _total = total;
  $('.mini-cart-subtotals.sub span.value').text(formatMoney(_total));
}

var buildModalCart = function(data) {
  var _html = '';
  var _total = 0;
  $.each(data, function(_, value) {
    _html += buildModalProductCart(value);
    _total += value.price;
  });
  miniCartTotals(_total);
  $('.mini-cart-products').html(_html);
  $('.mini-cart__content').show();
}
var app = {
  win: $(window),
  menuIsOpen: function() {
    return $('#navigation ul.navigation__menu').hasClass('active');
  },
  mobileToggle: function() {
    $('.toggleBtn__mobile--link').on('click', function() {
      $('#navigation ul.navigation__menu').toggleClass('active');
      $('i.icon_menu', this).toggleClass('closed');
      $('.toggleBtn__menu--item').hide();
      $('.navigation__menu--item').css({'display':'inline-block'});
    });
  },
  menuLevelOne: function() {
    $('.vertical-nav .subsubitems').hover(function() {
      var _this = this;
      $(_this).siblings().removeClass('active');
      $(_this).addClass('active');
    });
  },
  showMenuWraper: function() {
    $('.topnav').hover(function() {
      $('.navigation__overlay').removeClass('active');
      $('.navigation__menu--dropdown').removeClass('active');
      $('.menu-wrapper').removeClass('active');
    });
    if (this.win.width() > 667) {
      $('ul.navigation__menu > .navigation__menu--item > .navigation__menu--link').hover(function() {
        $(this).addClass('active');
        $('.navigation__overlay').addClass('active');
        $(this).next('.menu-wrapper').addClass('active');
        $(this).parent().siblings().find('.menu-wrapper').removeClass('active');
        $(this).parent().siblings().find('.navigation__menu--dropdown').removeClass('active');
        $(this).parent().siblings().find('.navigation__menu--link').removeClass('active');
      });
    } else {
      $('ul.navigation__menu > .navigation__menu--item > .navigation__menu--link').on('click', function() {
        $(this).toggleClass('active');
        $('.navigation__overlay').toggleClass('active');
        $(this).next('.menu-wrapper').toggleClass('active');
        $(this).parent().siblings().find('.menu-wrapper').removeClass('active');
        $(this).parent().siblings().find('.navigation__menu--dropdown').removeClass('active');
        $(this).parent().siblings().find('.navigation__menu--link').removeClass('active');
      });
    }
    $('.menu-wrapper').mouseleave(function(){
        $(this).removeClass('active');
        $('.navigation__overlay').removeClass('active');
        $('.navigation__menu--dropdown').removeClass('active');
    });
  },
  showMenuMobileWraper: function(e) {
    if (this.win.width() < 668) {
      $('.navigation__menu--dropdown .navigation__menu--link').on('click', function() {
        $(this).addClass('active');
        $('.navigation__overlay').addClass('active');
        $(this).next('.menu-wrapper').addClass('active');
        $(this).parent().siblings().find('.menu-wrapper').removeClass('active');
        $(this).parent().siblings().find('.navigation__menu--dropdown').removeClass('active');
        $(this).parent().siblings().find('.navigation__menu--link').removeClass('active');
      });
    }
  },
  resetMenuItems: function() {
    if(this.win.width() > 667) {
      $('.navigation__menu--item').css({'display':'inline-block'});
    }
  },
  displayMobileSearch: function() {
    $('.iconSearch__mobile-search-show').on('click', function() {
      $('#navigation ul.navigation__menu').toggleClass('active');
      $('.navigation__menu--item').hide();
      $('.toggleBtn__menu--item').show();
      $('i.icon_menu').removeClass('closed');
    });
  },
  changeHeaderOnScroll: function() {
    $(window).bind('scroll', function() {
      var win = $(window);
      if(win.scrollTop() > 10 && win.width() > 667) {
        $('.sticky_header').addClass('scrolled');
        $('body').css({'padding-top': '90px' });
      } else {
        $('.sticky_header').removeClass('scrolled');
        $('body').css({'padding-top': 0 });
      }
    });
  },
  activeSearchInput: function() {
    $('input.header-search__input').focus(function() {
      $(this).addClass('active');
    });
    $('input.header-search__input').focusout(function() {
      $(this).removeClass('active');
    });
    $('input.header-search__input').mouseleave(function() {
      $(this).removeClass('active');
      $(this).blur();
    });
  },
  messageSlider: function() {
    if($('.delivery-message').length) {
      $('.delivery-message').not('.slick-initialized').slick({
        infinite: true,
        dots: false,
        arrows: false,
        fade: false,
        centerMode: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        cssEase: 'ease-out',
        easing: 'swing',
        speed: 1200
      });
    }
  },
  homeslider: function() {
    if ($('.homeslider .slider').length) {
      $('.homeslider .slider').not('.slick-initialized').slick({
        infinite: true,
        dots: false,
        arrows: false,
        fade: true,
        centerMode: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 4000,
        cssEase: 'ease-out',
        easing: 'swing',
        speed: 1200
      });
    }
  },
  toggleGridFilters: function() {
    // Desktop
    $('.filters_bar_container.desktop .filters .js-show-by').on('click', function() {
      $(this).toggleClass('active');
      if ($(this).hasClass('active')) {
        $(this).text('Ocultar filtros');
        $('.products-container').addClass('col-sm-8 col-md-10 col-lg-9');
      } else {
        $(this).text('Mostrar filtros');
        $('.products-container').removeClass('col-sm-8 col-md-10 col-lg-9');
      }
      $('.products-grid__list-filters').toggleClass('active');
    });
  },
  toggleListFilter: function() {
    $('.filter-container__title').on('click', function() {
      $(this).toggleClass('expanded');
      $(this).next('.filter-container__list').toggleClass('active');
    });
    $('a.radio-btn').on('click', function() {
      $(this).toggleClass('active');
    });
  },
  toggleThumbnailProduct: function() {
    $('.js-grid-thumbnail-view').on('click', function() {
      $('.product-card__image-link').toggleClass('alternative');
      if ($('.product-card__image-link').hasClass('alternative')) {
        $(this).text('Vista del producto');
      } else {
        $(this).text('Vista en conjunto');
      }
    });
  },
  toggleInfoProduct: function() {
    $('.js-grid-thumbnail-info').on('click', function() {
      $('.product-card__description').toggleClass('visible');
      if ($('.product-card__description').hasClass('visible')) {
        $(this).text('Ocultar información');
      } else {
        $(this).text('Mostrar información');
      }
      $('.symmetrical').removeAttr('style');
      app.alignHeights();
    });
  },
  setHeightElement: function(boxArray, tallestElement) {
    for (var i = 0; i < boxArray.length; i++) {
      $(boxArray[i]).css({ 'height': tallestElement + 'px' });
    }
    boxArray = [];
  },
  alignHeights: function() {
    var tallestElement = 0,
      currentElementHeight = 0,
      boxArray = [],
      container = $('.symmetrical-container'),
      boxArray = container.children('.symmetrical');
    for (var i = 0; i < boxArray.length; i++) {
      $(boxArray[i]).css({ 'height': 'auto' });
      currentElementHeight = $(boxArray[i]).outerHeight();
      if (currentElementHeight > tallestElement) {
        tallestElement = currentElementHeight;
      }
    }
    setTimeout(this.setHeightElement(boxArray, tallestElement), 200);
  },
  toggleMiniGridThumb: function() {
    // Mini grid
    $('.js-grid-minigrid').on('click', function() {
      $('.product-card-container').removeClass('col-sm-3');
      $('.product-card-container').addClass('col-sm-2');
      $('.symmetrical').removeAttr('style');
      app.alignHeights();
    });
    // Grid
    $('.js-grid-grid').on('click', function() {
      $('.product-card-container').removeClass('col-sm-2');
      $('.product-card-container').addClass('col-sm-3');
      $('.symmetrical').removeAttr('style');
      app.alignHeights();
    });
  },
  getAjaxCart: function() {
    $('.mini-cart__link.mini-cart--is-full').hover(function() {
      if ($(window).width() > 667) {
        $.ajax({
          url:ajaxConfig.getUrl,
          type:'GET',
          success: function(data, textStatus, jqXHR) {
            AjaxCart = data.object || {};
            updateCartQty(AjaxCart.total_items);
            buildModalCart(AjaxCart.items);
          },
          error: function(data, textStatus, errorThrown) {
            console.log('message=:'+data+', text status=:'+textStatus+', error thrown:='+errorThrown)
          }})
        .success(function(){})
        .done(function(data){
          setTimeout(function(){
            closeCartModal();
          }, 2000);
        });
      }
    });
  },
 enableModalSize: function() {
    $('.sizeguide-tab--item a').on('click', function() {
      var _self = this;
      var _data = parseInt($(_self).data('origin'));
      var _content = $('.tab-content[data-content="'+_data+'"]');
      _content.addClass('active');
      _content.siblings().removeClass('active');
      $(_self).parent().addClass('active');
      $(_self).parent().siblings().removeClass('active');
    });
  },

  // Init methods
  init: function() {
    this.menuIsOpen();
    this.menuLevelOne();
    this.mobileToggle();
    this.displayMobileSearch();
    this.messageSlider();
    this.changeHeaderOnScroll();
    this.activeSearchInput();
    this.homeslider();
    this.showMenuWraper();
    this.showMenuMobileWraper();
    this.toggleGridFilters();
    this.toggleMiniGridThumb();
    this.toggleListFilter();
    this.toggleThumbnailProduct();
    this.toggleInfoProduct();
    this.getAjaxCart();
    this.enableModalSize();
  }
};

$(document).ready(function() {
  // Init
  app.init();

});
$(window).resize(function() {
  app.resetMenuItems();
})
.load(function() {
  app.alignHeights();
});
