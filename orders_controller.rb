class OrdersController < ApplicationController
	before_action :authenticate_user!

	def index
		@orders = Order.where(user_id: current_user.id).where(status: true)
	end

	def new
		if $cart.size === 0 || $cart === nil
			return redirect_to products_path
		end
		session[:paytransaction] = Digest::MD5.hexdigest(Random.new_seed.to_s)
		paytransaction = session[:paytransaction]

		$total = 0.00;
		$cart = session[:cart]

		$cart.each do | key, value |
			product = Product.find_by_id(key)
			$total += (product.price).to_f
  	end

		@order = Order.new
		@order.user_id = current_user.id
		@order.paytransaction = paytransaction
		@order.total = $total
		@order.status = 0
		@order.save

		$cart.each do | key, value |
			Product.find(key).orders << @order
		end

		if @order.save
		  session[:cart] = nil
			redirect_to payments_paypal_path(:order_id => @order.id, :total => @order.total)
		else
			redirect_to cart_path, :flash => { :alert => "Error try again" }
		end
	end

  def downloads
    images_path = File.join(Rails.root, "protected")
    download_file = Product.find(params[:id])
    type_file = params[:type_file]

    if download_file.blank?
      respond_with_error(:not_found)
    elsif(type_file == 'pdf')
      send_file(File.join(images_path, download_file.pdf_file), type: "application/pdf")
    elsif(type_file == 'epub')
      send_file(File.join(images_path, download_file.epub_file), type: "application/epub+zip")
    end
  end

  def success
    if session[:paytransaction].nil? || session[:paytransaction].empty?
       redirect_to root_path and return
    end
    if Payment.where(paymentid: strong_params[:paymentId]).first
		  session.delete(:paytransaction)
		  redirect_to orders_path
	  end
    @transaction = Payment.new(
	  paymentid: strong_params[:paymentId],
	  payerid: strong_params[:PayerID],
	  token: strong_params[:token])
	  @transaction.myipaddress = request.remote_ip
	  @transaction.paytransaction = session[:paytransaction]
	  @transaction.status = 1
	  @transaction.save
    
    if @transaction.save
      complete = Order.where(paytransaction: session[:paytransaction]).first
      complete.status = true
      complete.save
      session.delete(:paytransaction)
    end
  end

  def cancel
	  session.delete(:paytransaction)
  end

  private
  def strong_params
	  params.permit(:id, :paymentId, :token, :PayerID, :myipaddress, :paytransaction, :bookId)
  end
end
