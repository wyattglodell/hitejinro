<?php
		function submit_transaction($post, $total, $type = 'default')
		{
			$data = $this->sql->sanitize($post, URL);

			$authnet_values				= array
			(
				"x_login"				=> $this->conf->authorize_id,
				"x_version"				=> "3.1",
				"x_delim_char"			=> "|",
				"x_delim_data"			=> "TRUE",
				"x_url"					=> "FALSE",
				"x_type"				=> "AUTH_CAPTURE",
				"x_method"				=> "CC",
				"x_tran_key"			=> $this->conf->authorize_key,
				"x_relay_response"		=> "FALSE",
				"x_card_num"			=> $data['cc'],
				"x_exp_date"			=> $data['exp_date'],
				"x_description"			=> "Credit Card Transaction",
				"x_amount"				=> $total,
				"x_first_name"			=> $data['cc_first_name'],
				"x_last_name"			=> $data['cc_last_name'],
				"x_address"				=> $data['cc_address'],
				"x_city"				=> $data['cc_city'],
				"x_state"				=> $data['cc_state'],
				"x_zip"					=> $data['cc_zip'],
				'x_card_code'			=> $data['cvv']
			);
			$fields = "";
			foreach( $authnet_values as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";

 			$ch = curl_init("https://secure.authorize.net/gateway/transact.dll"); 
			curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
			curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); // use HTTP POST to send form data
			### curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response. ###
			$resp = curl_exec($ch); //execute post and get results
			curl_close ($ch); 
			
			$resp = explode('|', $resp);
			#$resp[0] = 1;
			if ($resp[0] == 1) { # GOOD
				return true;
			} else if ($resp[0] == 2) {
				return $resp[3];
			} else if ($resp[0] == 3) {
				return $resp[3];
			}
		}
?>