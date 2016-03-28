<?php
	require_once ($conf->custom.'/inc/paypalfunctions.php');
	
	$SandboxFlag = true;
	
	$paymentAmount = 100;
	$returnURL = "http://kenny.centricagency.com/srsqr/order-done";
	$cancelURL = "http://kenny.centricagency.com/srsqr/buy";	
	
	$conf->paypal['API_UserName']="kenny._1308336208_biz_api1.centric.com";
	$conf->paypal['API_Password']="DTP2PZJA8DX6B7RK";
	$conf->paypal['API_Signature']="AQXt9gQlmoidy9dLSaT1umzhyJ2HA1mJLvFAbny9khj6DmVjtJsFs6Iq";
	
	$conf->paypal['API_Version']= 64;
	$conf->paypal['sBNCode'] = "PP-ECWizard";

	if ($SandboxFlag == true) {
		$conf->paypal['API_Endpoint'] = "https://api-3t.sandbox.paypal.com/nvp";
		$conf->paypal['PAYPAL_URL'] = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout-mobile&useraction=commit&token=";
	} else {
		$conf->paypal['API_Endpoint'] = "https://api-3t.paypal.com/nvp";
		$conf->paypal['PAYPAL_URL'] = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout-mobile&useraction=commit&token=";
	}

	$currencyCodeType = "USD";
	$paymentType = "Order";
	
	$resArray = CallShortcutDirectCheckout ($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL);
	$ack = strtoupper($resArray["ACK"]);
	
	if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING") {
		RedirectToPayPal ( $resArray["TOKEN"] );
	} else  {
		$tpl->assign('resArray',$resArray);
	}
	
	$tpl->set_template('content','paypal.tpl.php');
?>