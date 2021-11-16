<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Modus Payment Page</title>
    
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/prettyPhoto.css" rel="stylesheet">
    <link href="css/price-range.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
   
	<style>
	    .sucess-msg{
	        margin-top: 20%;
	    }
	    .return-to-shopping{
	        margin-left: 50%;
	        margin-top: 10%;
	    }
	</style>
	</body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="sucess-msg text text-center text-success">
                    THANK YOU FOR PATRONIZING US! <br>
                    YOUR ORDER WILL BE DELIVERED TO YOUR LOCATION SHORTLY. <br>
                   
                </h1>
                <div >
                    <form style="margin-left: 42.5% " action="https://mudospharma.com/" method="GET">
                        @csrf
                        <input type="hidden" name="transaction_ID" value="{{Session::get('transaction_ID')}}"> <br>

                        <input style="margin: 0 auto !important; padding: 5px 20px; border-radius: 50px" class="align-middle "  type="submit" value="Continue Shopping">
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/jquery.js"></script>
	<script src="js/price-range.js"></script>
    <script src="js/jquery.scrollUp.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.prettyPhoto.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
