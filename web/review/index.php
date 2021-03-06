<?php
	/*
	 * Once the client is ready to be redirected to the payment page, we get all the information needed and initiate the transaction with PayGate.
	 * This checks that all the information is valid and that a transaction can take place.
	 * If the initiate is successful we are returned a request ID and a checksum which we will use to redirect the client to PayWeb3.
	 */

	/*
	 * Sessions used here only because we can't get the PayGate ID, Transaction reference and secret key on the result page.
	 */
	session_name('paygate_payweb3_testing_sample');
	session_start();

	include_once('../../lib/php/global.inc.php');

	/*
	 * Include the helper PayWeb 3 class
	 */
	require_once('../../lib/php/paygate.payweb3.php');
    include_once('../../lib/php/config.inc.php');

	$total_amount = "";

	// how man
	$total_2days = FULL_2DAY_PRICE * (isset($_POST['FULL_2DAY']) ? filter_var($_POST['FULL_2DAY'], FILTER_SANITIZE_NUMBER_INT) : 0);
	$total_3days = FULL_3DAY_PRICE * (isset($_POST['FULL_3DAY']) ? filter_var($_POST['FULL_3DAY'], FILTER_SANITIZE_NUMBER_INT) : 0);
	$total_live = LIVESTREAM_PRICE * (isset($_POST['LIVESTREAM']) ? filter_var($_POST['LIVESTREAM'], FILTER_SANITIZE_NUMBER_INT) : 0);
	$total_students = STUDENT_2DAY_PRICE * (isset($_POST['STUDENT_2DAY']) ? filter_var($_POST['STUDENT_2DAY'], FILTER_SANITIZE_NUMBER_INT) : 0);
	$total_amount = $total_2days + $total_3days + $total_students + $total_live;

	if(TEST_PURCHASE==1){
		$total_amount = 2;
	}

	$orderId = md5(getDateTime('Y-m-d H:i:s'));
	// WHERE SHOULD PAYGET SEND US BACK TO?
	$paygateReturnURL = REGISTER_HOST_URL . '/complete/?t=' . $orderId;

	$mandatoryFields = array(
		'PAYGATE_ID'        => PAYGATE_ID,
		'REFERENCE'         => generateReference(),
		'AMOUNT'            => filter_var($total_amount*100, FILTER_SANITIZE_NUMBER_INT),
		'CURRENCY'          => "ZAR",
		'RETURN_URL'        => $paygateReturnURL,
		'TRANSACTION_DATE'  => getDateTime('Y-m-d H:i:s'),
		'LOCALE'            => 'en-za',
		'COUNTRY'           => filter_var($_POST['COUNTRY'], FILTER_SANITIZE_STRING),
		'EMAIL'             => filter_var($_POST['EMAIL'], FILTER_SANITIZE_EMAIL)
	);


	$optionalFields = array(
		'PAY_METHOD'        => (isset($_POST['PAY_METHOD']) ? filter_var($_POST['PAY_METHOD'], FILTER_SANITIZE_STRING) : ''),
		'PAY_METHOD_DETAIL' => (isset($_POST['PAY_METHOD_DETAIL']) ? filter_var($_POST['PAY_METHOD_DETAIL'], FILTER_SANITIZE_STRING) : ''),
		'NOTIFY_URL'        => NOTIFY_HOST_URL . "/api/order/$orderId/notifyUpdate",
		'USER1'             => $orderId,
		'USER2'             => (isset($_POST['USER2']) ? filter_var($_POST['USER2'], FILTER_SANITIZE_URL) : ''),
		'USER3'             => (isset($_POST['USER3']) ? filter_var($_POST['USER3'], FILTER_SANITIZE_URL) : ''),
		'VAULT'             => (isset($_POST['VAULT']) ? filter_var($_POST['VAULT'], FILTER_SANITIZE_NUMBER_INT) : ''),
		'VAULT_ID'          => (isset($_POST['VAULT_ID']) ? filter_var($_POST['VAULT_ID'], FILTER_SANITIZE_STRING) : '')
	);

	$ticketFields = array(
		'buyerName'         => (isset($_POST['NAME']) ? filter_var($_POST['NAME'], FILTER_SANITIZE_STRING) : ''),
		'buyerEmail'        => (isset($_POST['EMAIL']) ? filter_var($_POST['EMAIL'], FILTER_SANITIZE_EMAIL) : ''),
		'buyerPhone'        => (isset($_POST['PHONE']) ? $_POST['PHONE'] : ''),
		'buyerCompany'      => (isset($_POST['COMPANY']) ? filter_var($_POST['COMPANY'], FILTER_SANITIZE_STRING) : ''),
		'full_2day'         => (isset($_POST['FULL_2DAY']) ? filter_var($_POST['FULL_2DAY'], FILTER_SANITIZE_NUMBER_INT) : ''),
		'full_3day'         => (isset($_POST['FULL_3DAY']) ? filter_var($_POST['FULL_3DAY'], FILTER_SANITIZE_NUMBER_INT) : ''),
		'earlyBird_2day'    => (isset($_POST['EARLY_BIRD_2DAY']) ? filter_var($_POST['EARLY_BIRD_2DAY'], FILTER_SANITIZE_NUMBER_INT) : ''),
		'earlyBird_3day'    => (isset($_POST['EARLY_BIRD_3DAY']) ? filter_var($_POST['EARLY_BIRD_3DAY'], FILTER_SANITIZE_NUMBER_INT) : ''),
		'student_2day'    	=> (isset($_POST['STUDENT_2DAY']) ? filter_var($_POST['STUDENT_2DAY'], FILTER_SANITIZE_NUMBER_INT) : ''),
		'livestream'    	=> (isset($_POST['LIVESTREAM']) ? filter_var($_POST['LIVESTREAM'], FILTER_SANITIZE_NUMBER_INT) : ''),
		'orderAmount'       => $total_amount,
		'orderId'          	=> $orderId
	);

	$data = array_merge($mandatoryFields, $optionalFields);
	$fullData = array_merge($data, $ticketFields);
	$backURL = "e2d=" . $fullData['earlyBird_2day']
			. "&e3d=" . $fullData['earlyBird_3day']
			. "&f2d=" . $fullData['full_2day']
			. "&f3d=" . $fullData['full_3day']
			. "&s2d=" . $fullData['student_2day']
			. "&livestream=" . $fullData['livestream']
			. "&name=" . $fullData['buyerName']
			. "&email=" . $fullData['buyerEmail']
			. "&phone=" . $fullData['buyerPhone']
			. "&company=" . $fullData['buyerCompany']
			. "&country=" . $fullData['buyerName'];

	if(isset($_POST['thehookup'])) {
		$backURL = $backURL . "&thehookup=1";
	}

	// check if we need to go back
	// is this a student?
	if(isset($_POST['STUDENT_2DAY'])){
		$backURL = $backURL . "&t=s2d";
		if($_POST['STUDENT_2DAY']==''){
			header("Location: /?err=5&$backURL");
			die();
		}
	} elseif(($ticketFields['full_2day']=='' && $ticketFields['full_3day']=='' && $ticketFields['livestream']=='') || $total_amount == 0){
		header("Location: /?err=5&$backURL");
		die();
	}
	saveTicketOrder($fullData, TICKETS_HOST_URL);


	/*
	 * Set the session vars once we have cleaned the inputs
	 */
	$_SESSION['pgid']      = $data['PAYGATE_ID'];
	$_SESSION['reference'] = $data['REFERENCE'];
	$_SESSION['key']       = ENKI;

	/*
	 * Initiate the PayWeb 3 helper class
	 */
	$PayWeb3 = new PayGate_PayWeb3();
	/*
	 * if debug is set to true, the curl request and result as well as the calculated checksum source will be logged to the php error log
	 */
	//$PayWeb3->setDebug(true);
	/*
	 * Set the encryption key of your PayGate PayWeb3 configuration
	 */
	$PayWeb3->setEncryptionKey(ENKI);
	/*
	 * Set the array of fields to be posted to PayGate
	 */
	$PayWeb3->setInitiateRequest($data);

	/*
	 * Do the curl post to PayGate
	 */
	$returnData = $PayWeb3->doInitiate();


	// INCLUDE THE HTML HEADER
	$pageTitle = "Review your tickets";
	include_once('../../lib/php/header.inc.php');
?>
	<body class="body--tickets">
		<?php include_once('../../lib/php/top-bar.inc.php'); ?>
		<div class="content-wrapper">
			<div class="container-fluid" style="min-width: 320px;">
				<div class="hero--page">
				  	<h1 class="heading--centered">Almost done!</h1>
				  	<p class="text--centered caption caption--hug">Have a quick check of everything.</p>
				</div>
				<div class="single">

				<h6 class="small-title small-title--light"><br />YOUR TICKETS</h6>
				<ul class="no-bullet">
					<?php if((isset($_POST['LIVESTREAM']) && $_POST['LIVESTREAM'] > 0)) : ?>
                    <li class="ticket ticket--flex ticket--review">
                        <div class="ticket__description-wrapper">
                            <label class="ticket__name" for="ticket-ihqxk9qgdry">
                                <?php echo $_POST['LIVESTREAM'] ?> x Livestream Pass
                            </label>
                        </div>
                        <div class="ticket__detail">
                            <div class="ticket__price ticket__detail__item">
                                <span>
                                R <?php echo number_format($total_live) ?>
                                </span>
                            </div>
                        </div>
                    </li>
					<?php endif ?>
					<?php if((isset($_POST['FULL_3DAY']) && $_POST['FULL_3DAY'] > 0)) : ?>
                    <li class="ticket ticket--flex ticket--review">
                        <div class="ticket__description-wrapper">
                            <label class="ticket__name" for="ticket-ihqxk9qgdry">
                                <?php echo $_POST['FULL_3DAY'] ?> x 3 Day Pass
                            </label>
                        </div>
                        <div class="ticket__detail">
                            <div class="ticket__price ticket__detail__item">
                                <span>
                                R <?php echo number_format($total_3days) ?>
                                </span>
                            </div>
                        </div>
                    </li>
					<?php endif ?>
					<?php if(isset($_POST['FULL_2DAY']) && $_POST['FULL_2DAY'] > 0) : ?>
                    <li class="ticket ticket--flex ticket--review">
                        <div class="ticket__description-wrapper">
                            <label class="ticket__name" for="ticket-ihqxk9qgdry">
                                <?php echo $_POST['FULL_2DAY'] ?> x 2 Day Pass
                            </label>
                        </div>
                        <div class="ticket__detail">
                            <div class="ticket__price ticket__detail__item">
                                <span>
                                R <?php echo number_format($total_2days) ?>
                                </span>
                            </div>
                        </div>
                    </li>
					<?php endif ?>
					<?php if(isset($_POST['STUDENT_2DAY']) && $_POST['STUDENT_2DAY'] > 0) : ?>
                    <li class="ticket ticket--flex ticket--review">
                        <div class="ticket__description-wrapper">
                            <label class="ticket__name" for="">
                                <?php echo $_POST['STUDENT_2DAY'] ?> x 2 Day Student Pass
                            </label>
                        </div>
                        <div class="ticket__detail">
                            <div class="ticket__price ticket__detail__item">
                                <span>
                                R <?php echo number_format($total_students) ?>
                                </span>
                            </div>
                        </div>
                    </li>
					<?php endif ?>
                    <li class="ticket ticket--flex ticket--review">
                        <div class="ticket__description-wrapper">
                            <label class="ticket__name" for="ticket-ihqxk9qgdry">
                                <strong>TOTAL</strong>
                            </label>
                        </div>
                        <div class="ticket__detail">
                            <div class="ticket__price ticket__detail__item">
                                <span>
                                  <strong>R <?php echo number_format($total_amount) ?></strong>
                                </span>
                            </div>
                        </div>
                    </li>
                </ul>
				<br/>
				<p style="font-size: .7rem" >
					We'll email the receipt to <?php echo $ticketFields['buyerName'] ?>
					<?php if($ticketFields['buyerCompany']!=''){
							echo '(' . $ticketFields['buyerCompany'] . ')' ;
						}
					?>
					— <?php echo $ticketFields['buyerEmail'] ?>.
				</p>
				<br/>
				<form role="form" action="<?php echo $PayWeb3::$process_url ?>" method="post" name="paygate_process_form">
					<div class="button-wrapper">
						<a style="font-size: .7rem" href="/?<?php echo $backURL ?>">Back to tickets</a>
						<input type="submit" class="button button-primary btn-form button--block" type="submit" name="btnSubmit" value="Pay with credit card">
					</div>

					<?php if(isset($PayWeb3->processRequest) || isset($PayWeb3->lastError)){ ?>
					<?php
						if (!isset($PayWeb3->lastError)) {
							/*
							 * It is not an error, so continue
							 */

							/*
							 * Check that the checksum returned matches the checksum we generate
							 */
							$isValid = $PayWeb3->validateChecksum($PayWeb3->initiateResponse);

							if($isValid){
								/*
								 * If the checksums match loop through the returned fields and create the redirect from
								 */
								foreach($PayWeb3->processRequest as $key => $value){
									echo <<<HTML
					<input type="hidden" name="{$key}" value="{$value}" />
HTML;
								}
							} else {
								echo 'Checksums do not match';
							}
						}
						/*
						 * Submit form as/when needed
						 */
						?>
					<br>
					<?php } ?>
					<br>
				</form>
				<?php include_once('../../lib/php/payments-footer.inc.php'); ?>
			</div>
			</div>
		</div>
		<script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
	</body>
</html>
