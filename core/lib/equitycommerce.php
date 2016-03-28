<?php
		function submit_transaction($post, $total)
		{
			$data = $this->sql->sanitize($post, URL);
			
			$authnet_values				= array
			(
				"type"					=> "sale",
				"ccnumber"				=> $data['cc'],
				"ccexp"					=> $data['exp_date'],
				"redirect"				=> "",
				'username'				=> $this->conf->gateway_username,
				'password'				=> $this->conf->gateway_password,
				"amount"				=> $total,
				'firstname'				=> $data['cc_first_name'],
				'lastname'				=> $data['cc_last_name'],
				'address1'				=> $data['cc_address'],
				'city'					=> $data['cc_city'],
				'state'					=> $data['cc_state'],
				'zip'					=> $data['cc_zip']
			);
			$fields = "";
			foreach( $authnet_values as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";

 			$ch = curl_init("https://secure.equitycommercegateway.com/api/transact.php"); 
			curl_setopt($ch, CURLOPT_HEADER, true); // set to 0 to eliminate header info from response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
			curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); // use HTTP POST to send form data
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response. ###
			$resp = curl_exec($ch); //execute post and get results
			curl_close ($ch); 
			
			$resp = explode("\n", $resp);
			
			$resp = end($resp);
			
			$field = explode('&', $resp);
			
			if ($field) {	
				$resp = array();
				foreach ($field as $v)
				{
					$part = explode('=', $v);
					if ($part) {
						$resp[ $part[0] ] = $part[1];
					}	
				}
				
				return $resp;
			} else {
				return 'No response received';
			}
		}
		
		function get_response_text($code) # RESPONSE TEXT FOR EQUITY COMMERCE
		{	
			switch ($code) 
			{
				case 100 : $r = 'Transaction was approved'; break;
				case 200 : $r = 'Transaction was declined by issuer'; break;
				case 201 : $r = 'Do not honor'; break;
				case 202 : $r = 'Unsufficient Funds'; break;
				case 203 : $r = 'Over the limit'; break;
				case 204 : $r = 'Transaction not allowed'; break;
				case 220 : $r = 'Incorrect payment data'; break;
				case 221 : $r = 'No such card issuer'; break;
				case 222 : $r = 'No card number on file with issuer'; break;
				case 223 : $r = 'Expired card'; break;
				case 224 : $r = 'Invalid expiration date'; break;
				case 225 : $r = 'Invalid card security code'; break;
				case 240 : $r = 'Call issuer for further information'; break;
				case 250 : $r = 'Pickup Card'; break;
				case 251 : $r = 'Lost card'; break;
				case 252 : $r = 'Stolen Card'; break;
				case 253 : $r = 'Fraudulant Card'; break;
				case 260 : $r = 'Declined with further instructions available'; break;
				case 261 : $r = 'Declined - Stop all recurring payments'; break;
				case 262 : $r = 'Declined - Stop this recurring program'; break;
				case 263 : $r = 'Declined - Update cardholder data available'; break;
				case 300 : $r = 'Transaction was rejected by gateway'; break;
				default : $r = 'An error occured with the transaction ('.$code.'), please contact us for help'; break;
			}
			return $r;
		}
?>