<?php 

namespace Drupal\pipedrive_api;

class PipedriveQueue {


	public function pipedrive_pull($last_pull) {

		// Pipedrive API token
		// Decrypt the API Key
        $ciphertext = \Drupal::config('pipedrive_api.settings')->get('api_key');
        $api_key_decode = base64_decode($ciphertext);
        $cipher = "AES-256-CBC";
        $key = \Drupal::config('system.site')->get('encrypt_drupal_variable_key');
        $iv = \Drupal::config('system.site')->get('encrypt_decrypt_iv');
        $api_token = openssl_decrypt($api_key_decode, $cipher, $key, OPENSSL_RAW_DATA, $iv);

		// Pipedrive company domain
		$company_domain = \Drupal::config('pipedrive_api.settings')->get('company_domain');
		 
		//URL for Deal listing with your $company_domain and $api_token variables
		$url = 'https://'.$company_domain.'.pipedrive.com/api/v1/persons?api_token=' . $api_token;

		$fieldurl = 'https://'.$company_domain.'.pipedrive.com/api/v1/personFields?api_token=' . $api_token;
		 
		//GET request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		$output = curl_exec($ch);
		curl_close($ch);

		// Get person fields
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $fieldurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		$fieldoutput = curl_exec($ch);
		curl_close($ch);
		 
		// Create an array from the data that is sent back from the API
		// As the original content from server is in JSON format, you need to convert it to a PHP array
		$result = json_decode($output, true);
		$fieldresult = json_decode($fieldoutput, true);

		// Check if data returned in the result is not empty
		if (empty($result['data'])) {
		    exit('No contacts created yet' . PHP_EOL);
		}

		foreach ($fieldresult['data'] as $key => $field) {
			$field_name = $field['name'];
			if ($field_name == 'Campaign') {
				$campaign_options = $field['options'];
			}
		}

		$new_persons = array();
		// Iterate over all found deals
		foreach ($result['data'] as $key => $person) {
		    $email = $person['email'][0]['value'];
		    $added = $person['add_time'];
		    $added = strtotime($added);
		    $updated = $person['update_time'];
		    $updated = strtotime($updated);
			
		    $tags = array();

		    // Campaign Field API KEY
		    // 
		    $campaigns = explode(",", $person['XXXXXXXXXXXXXX']); // Tag value hidden for client privacy

		    // Get Campaign Selections
		    foreach ($campaign_options as $key => $options) {
			    foreach ($campaigns as $key => $item ) {
			    	if ($options['id'] == $item) {
				    	$campaign_label = $options['label'];
				    	array_push($tags, array(
				    		"name" => $campaign_label,
				    		"status" => "active"
				    	));
				    }
			    }
		    }

		    if ($campaign == '15') {
		    	$labeltext = 'Customer';
		    } else if ($label == '6') {
		    	$labeltext = 'Hot Lead';
		    } else if ($label == '7') {
		    	$labeltext = 'Warm Lead';
		    } else if ($label == '8') {
		    	$labeltext = 'Cold Lead';
		    }
		  
		    $person['addedtags'] = $tags;

			if (($added > $last_pull) || ($updated > $last_pull))
			{
				if (($email != 'NULL') && ($email != 'undefined') && ($email != '') && (empty($email) != TRUE)) {
					array_push($new_persons, $person);
				}
			}
		}
		ddl($new_persons);
		return $new_persons;

	}
}



?>