class PaymentsController < ApplicationController
	before_action :authenticate_user!, only: [:paypal]
	require 'paypal-sdk-rest'
	include PayPal::SDK::REST
	include PayPal::SDK::Core::Logging
  def paypal
	  PayPal::SDK::Core::Config.load('config/paypal.yml',  ENV['RACK_ENV'] || 'production')
	  PayPal::SDK.logger = Logger.new(STDERR)
	  PayPal::SDK.logger.level = Logger::INFO

	  order_id = params[:id]
	  $total = params[:total]
    $total = sprintf "%.2f", $total

	  @payment = Payment.new({
	    :intent =>  "sale",
	    :payer =>  {
	      :payment_method =>  "paypal" },

	    :redirect_urls => {
	    :return_url => "http://www.playcreativepiano.com/#{I18n.locale}/orders/success",
	    :cancel_url => "http://www.playcreativepiano.com/#{I18n.locale}/orders/cancel"
      },
	    :transactions =>  [{
	      :item_list => {
	        :items => [{
	          :name => "Order from Play Creative Piano:",
	          :sku => order_id,
	          :price => $total,
	          :currency => "USD",
	          :quantity => 1 }]},
	      :amount =>  {
	        :total =>  $total,
	        :currency =>  "USD" },
	      :description =>  "This is the payment transaction description." }]})

  if @payment.create
  		@payment.id
  		session[:cart] = nil
	    @redirect_url = @payment.links.find{|v| v.method == "REDIRECT" }.href
	    logger.info "Payment[#{@payment.id}]"
	    logger.info "Redirect: #{@redirect_url}"
	  else
		@payment.error
	    logger.error @payment.error.inspect
	  end
	  redirect_to @redirect_url
	end
end
