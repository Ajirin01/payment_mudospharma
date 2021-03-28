<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Modus Payment Page</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/prettyPhoto.css" rel="stylesheet">
    <link href="css/price-range.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
	<link href="css/responsive.css" rel="stylesheet">

<!--<form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
    <div class="row" style="margin-bottom:40px;">
      <div class="col-md-8 col-md-offset-2">
        <p>
            <div>
                Mudos Payment Pages
            </div>
        </p>
        <input type="email" name="email" value=""> {{-- required --}}
        <input type="hidden" name="orderID" value="345">
        <input type="number" name="amount" value=""> {{-- required in kobo --}}
        <input type="hidden" name="quantity" value="3">
        <input type="hidden" name="metadata" value="{{ json_encode($array = ['key_name' => 'value',]) }}" > {{-- For other necessary things you want to add to your payload. it is optional though --}}
        <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
        <input type="hidden" name="key" value="{{ config('paystack.secretKey') }}"> {{-- required --}}
        {{ csrf_field() }} {{-- works only when using laravel 5.1, 5.2 --}}

         <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- employ this in place of csrf_field only in laravel 5.0 --}}


        <p>
          <button class="btn btn-success btn-lg btn-block" type="submit" value="Pay Now!">
          <i class="fa fa-plus-circle fa-lg"></i> Pay Now!
          </button>
        </p>
      </div>
    </div>
</form>
-->

<section id="form" >
		<div class="container">
			<div class="row">
<div class="col-sm-6 col-sm-offset-3 text-center">
					<div class="signup-form">
					    <h1 id="counter" style="font-size: 15rem"></h1>
					    
						<h2>Please wait...</h2>
						<form id="pay-form" method="POST" action="{{ route('pay') }}" accept-charset="UTF-8"  style="display:none" >
							<input type="text"  name="orderID" placeholder="Name" value="{{$name}}" required/>
							<input type="email" name="email" placeholder="Email Address" value="{{$email}}" required/>
							<input type="number" name="amount" value="{{$total_price*100}}"  readonly/><span class="text text-success">Note that this amount is in KOBO</span>
							<input type="hidden" name="metadata" value="{{ json_encode($array = ['key_name' => 'value',]) }}" />
							<input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"/>
							<input type="hidden" name="key" value="{{ config('paystack.secretKey') }}">{{ csrf_field() }}
							  <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
							<button type="submit" class="btn btn-default text-center " value="Pay Now!">Pay Now</button>
						</form>
					</div>
					</div>
		</div>
	</section>
 <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script type="text/javascript">
$(function() {

    var $form = $(".require-validation");

    $('form.require-validation').bind('submit', function(e) {
        var $form         = $(".require-validation"),
        inputSelector = ['input[type=email]', 'input[type=password]',
                         'input[type=text]', 'input[type=file]',
                         'textarea'].join(', '),
        $inputs       = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid         = true;
        $errorMessage.addClass('hide');

        $('.has-error').removeClass('has-error');
        $inputs.each(function(i, el) {
          var $input = $(el);
          if ($input.val() === '') {
            $input.parent().addClass('has-error');
            $errorMessage.removeClass('hide');
            e.preventDefault();
          }
        });

        if (!$form.data('cc-on-file')) {
          e.preventDefault();
          Stripe.setPublishableKey($form.data('stripe-publishable-key'));
          Stripe.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
          }, stripeResponseHandler);
        }

  });

  function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('hide')
                .find('.alert')
                .text(response.error.message);
        } else {
            /* token contains id, last4, and card type */
            var token = response['id'];

            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }

});

    
	
	</script>
	
	 <script>
	    var counter = document.getElementById('counter')
        var pay_form = document.getElementById('pay-form')
        
        var count = 5
        
        counter.innerText = count
        
        setInterval(function(){
            count = count - 1
            counter.innerText = count;
            if(count == 0){
                counter.style.display="none"
                pay_form.submit()
            }
        }, 1000)
	 </script>
	
	
  <script src="js/jquery.js"></script>
	<script src="js/price-range.js"></script>
    <script src="js/jquery.scrollUp.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.prettyPhoto.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
