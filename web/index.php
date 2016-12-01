<?php
	/*
	 * This is an example page of the form fields required for a PayGate PayWeb 3 transaction.
	 */

<<<<<<< HEAD
  // If in testing mode use the sandbox domain ?  sandbox.payfast.co.za else www.payfast.co.za
   $testingMode = true;
   $pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';

   $passPhrase = "reflexions";
    define('BASE_URL', 'https://register.pixelup.co.za/');
    
    $pfOutput = '';
    $cartTotal = 6500.00;
    $data = array(
      // Merchant details
      'merchant_id' => '10002948',
      'merchant_key' => '0z5ngcoyonv4o',
      'return_url' => BASE_URL . 'thanks.php?8542',
      'cancel_url' => BASE_URL . 'cancelled.php',
      'notify_url' => BASE_URL . 'itn.php',
          'name_first' => 'First Name',
          'name_last'  => 'Last Name',
          'email_address'=> 'farai@pixelup.co.za',
          'm_payment_id' => '8542', //Unique payment ID to pass through to notify_url
          'amount' => number_format( $cartTotal, 2, '.', '' ), //Amount needs to be in ZAR,if you have a multicurrency system, the conversion needs to place before building this array
      'item_name' => 'Item Name',
      'item_description' => 'Item Description',
      'custom_int1' => '9586', //custom integer to be passed through
      'custom_str1' => 'custom string to be passed through with the transaction to the notify_url page'
      );

  // Create GET string
  foreach( $data as $key => $val )
  {
      if(!empty($val))
      {
        $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
      }
}
  // Remove last ampersand
  $getString = substr( $pfOutput, 0, -1 );
  if( isset( $passPhrase ) )
  {
      $getString .= '&passphrase='.$passPhrase;
  }
  $data['signature'] = md5( $getString );

   $htmlForm = '
<form action="https://'.$pfHost.'/eng/process" method="post">'; foreach($data as $name=> $value) { $htmlForm .= '<input name="'.$name.'" type="hidden" value="'.$value.'" />'; } $htmlForm .= '<input type="submit" value="Pay Now" /></form>'; echo $htmlForm;
=======
	/*
	 * Sessions used here only because we can't get the PayGate ID, Transaction reference and secret key on the result page.
	 *
	 * First input so we make sure there is nothing in the session.
	 */
	session_name('paygate_payweb3_testing_sample');
	session_start();
	session_destroy();
>>>>>>> payWeb

	include_once('../lib/php/global.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<title>PayWeb 3 - Initiate</title>
		<link rel="stylesheet" href="/css/bootstrap.min.css">
		<link rel="stylesheet" href="/css/core.css">
	</head>
	<body>
		<div class="container-fluid" style="min-width: 320px;">
			<nav class="navbar navbar-inverse navbar-fixed-top">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="">
							<img alt="PayGate" src="/images/paygate_logo_mini.png" />
						</a>
						<span style="color: #f4f4f4; font-size: 18px; line-height: 45px; margin-right: 10px;"><strong>PayWeb 3</strong></span>
					</div>
					<div class="collapse navbar-collapse" id="navbar-collapse">
						<ul class="nav navbar-nav">
							<li class="active">
								<a href="/index.php">Initiate</a>
							</li>
							<li>
								<a href="/query.php">Query</a>
							</li>
							<li>
								<a href="/simple_initiate.php">Simple initiate</a>
							</li>
						</ul>
					</div>
				</div>
			</nav>
			<div style="background-color:#80b946; text-align: center; margin-top: 51px; margin-bottom: 15px; padding: 4px;"><strong>Step 1: Initiate</strong></div>
			<div class="container">
				<form role="form" class="form-horizontal text-left" action="request.php" method="post" name="paygate_initiate_form">
					<div class="form-group">
						<label for="PAYGATE_ID" class="col-sm-3 control-label">PayGate ID</label>
						<div class="col-sm-6">
							<input type="text" name="PAYGATE_ID" id="PAYGATE_ID" class="form-control" value="10011072130" />
						</div>
					</div>
					<div class="form-group">
						<label for="REFERENCE" class="col-sm-3 control-label">Reference</label>
						<div class="col-sm-6">
							<input type="text" name="REFERENCE" id="REFERENCE" class="form-control" value="<?php echo generateReference(); ?>" />
						</div>
					</div>
					<div class="form-group">
						<label for="AMOUNT" class="col-sm-3 control-label">Amount</label>
						<div class="col-sm-6">
							<input type="text" name="AMOUNT" id="AMOUNT" class="form-control" value="100" />
						</div>
					</div>
					<div class="form-group">
						<label for="CURRENCY" class="col-sm-3 control-label">Currency</label>
						<div class="col-sm-6">
							<input type="text" name="CURRENCY" id="CURRENCY" class="form-control" value="ZAR" />
						</div>
					</div>
					<div class="form-group">
						<label for="RETURN_URL" class="col-sm-3 control-label">Return URL</label>
						<div class="col-sm-6">
							<input type="text" name="RETURN_URL" id="RETURN_URL" class="form-control" value="<?php echo $fullPath['protocol'] . $fullPath['host'] . '/' . 'result.php'; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label for="TRANSACTION_DATE" class="col-sm-3 control-label">Transaction Date</label>
						<div class="col-sm-6">
							<input type="text" name="TRANSACTION_DATE" id="TRANSACTION_DATE" class="form-control" value="<?php echo getDateTime('Y-m-d H:i:s'); ?>" />
						</div>
					</div>
					<div class="form-group">
						<label for="LOCALE" class="col-sm-3 control-label">Locale</label>
						<div class="col-sm-6">
							<input type="text" name="LOCALE" id="LOCALE" class="form-control" value="en-za" />
						</div>
					</div>
					<div class="form-group">
						<label for="COUNTRY" class="col-sm-3 control-label">Country</label>
						<div class="col-sm-6">
							<select name="COUNTRY" id="COUNTRY" class="form-control">
								<?php echo generateCountrySelectOptions(); ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="EMAIL" class="col-sm-3 control-label">Customer Email</label>
						<div class="col-sm-6">
							<input type="text" name="EMAIL" id="EMAIL" class="form-control" value="support@paygate.co.za" />
						</div>
					</div>
					<br>
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-4">
							<button type="button" class="btn btn-primary btn-block" data-toggle="collapse" data-target="#extraFieldsDiv" aria-expanded="false" aria-controls="extraFieldsDiv">
								Extra Fields
							</button>
						</div>
					</div>
					<div id="extraFieldsDiv" class="collapse well well-sm">
						<div class="form-group">
							<label for="PAY_METHOD" class="col-sm-3 control-label">Pay Method</label>
							<div class="col-sm-6">
								<input type="text" name="PAY_METHOD" id="PAY_METHOD" class="form-control" placeholder="optional" />
							</div>
						</div>
						<div class="form-group">
							<label for="PAY_METHOD_DETAIL" class="col-sm-3 control-label">Pay Method Detail</label>
							<div class="col-sm-6">
								<input type="text" name="PAY_METHOD_DETAIL" id="PAY_METHOD_DETAIL" class="form-control" placeholder="optional" />
							</div>
						</div>
						<div class="form-group">
							<label for="NOTIFY_URL" class="col-sm-3 control-label">Notify URL</label>
							<div class="col-sm-6">
								<input type="text" name="NOTIFY_URL" id="NOTIFY_URL" class="form-control" placeholder="optional" value="https://hooks.zapier.com/hooks/catch/1239813/trkqnr/" />
							</div>
						</div>
						<div class="form-group">
							<label for="USER1" class="col-sm-3 control-label">User Field 1</label>
							<div class="col-sm-6">
								<input type="text" name="USER1" id="USER1" class="form-control" placeholder="optional" />
							</div>
						</div>
						<div class="form-group">
							<label for="USER2" class="col-sm-3 control-label">User Field 2</label>
							<div class="col-sm-6">
								<input type="text" name="USER2" id="USER2" class="form-control" placeholder="optional" />
							</div>
						</div>
						<div class="form-group">
							<label for="USER3" class="col-sm-3 control-label">User Field 3</label>
							<div class="col-sm-6">
								<input type="text" name="USER3" id="USER3" class="form-control" placeholder="optional" />
							</div>
						</div>
						<div class="form-group">
							<label for="VAULT" class="col-sm-3 control-label">Vault</label>
							<div class="col-sm-6">
								<div class="radio">
									<label>
										<input type="radio" name="VAULT" id="VAULTOFF" value="" checked>
										No card Vaulting
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="VAULT" id="VAULTNO" value="0">
										Don't Vault card
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="VAULT" id="VAULTYES" value="1">
										Vault card
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="VAULT_ID" class="col-sm-3 control-label">Vault ID</label>
							<div class="col-sm-6">
								<input type="text" name="VAULT_ID" id="VAULT_ID" class="form-control" placeholder="optional" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="encryption_key" class="col-sm-3 control-label">Encryption Key</label>
						<div class="col-sm-6">
							<input type="text" name="encryption_key" id="encryption_key" class="form-control" value="secret" />
						</div>
					</div>
					<br>
					<div class="form-group">
						<div class=" col-sm-offset-4 col-sm-4">
							<input type="submit" name="btnSubmit" class="btn btn-success btn-block" value="Calculate Checksum" />
						</div>
					</div>
					<br>
				</form>
			</div>
		</div>
		<script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="/js/bootstrap.min.js"></script>
	</body>
</html>
