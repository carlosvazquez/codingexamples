class CartController < ApplicationController
  #before_action :set_cart
  #before_action :authenticate_user!, except: [:index]

    def add
      id = params[:id]

      if session[:cart] then
        $cart = session[:cart]
        flash[:notice] = t('store.flash.item_added')
      else
        session[:cart] = {}
        $cart = session[:cart]
      end
      if $cart[id] then
        $cart[id] = $cart[id] + 0
        flash[:notice] = t('store.flash.item_no_added')
      else
        $cart[id] = 1
      end
      redirect_to :action => :index
    end

    def clearCart
      session[:cart] = nil
      redirect_to products_path
    end

    def index
      if session[:cart] then
        $cart = session[:cart]
      else
        $cart = {}
      end
    end

end
