<?php 

namespace Drupal\pipedrive_api;

class MailchimpPush {

	public function add_to_audience($new_queue) {

		require_once('/vendor/autoload.php');

		// Mailchimp API token
		// Decrypt the API Key
        $mailchimpciphertext = \Drupal::config('pipedrive_api.settings')->get('mailchimp_api_key');
        $api_key_decode = base64_decode($mailchimpciphertext);
        $cipher = "AES-256-CBC";
        $key = \Drupal::config('system.site')->get('encrypt_drupal_variable_key');
        $iv = \Drupal::config('system.site')->get('encrypt_decrypt_iv');
        $api_token = openssl_decrypt($api_key_decode, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        // Mailchimp Server
		$mailchimp_server = \Drupal::config('pipedrive_api.settings')->get('mailchimp_server');

		// Mailchimp List ID
		$mailchimp_listid = \Drupal::config('pipedrive_api.settings')->get('mailchimp_listid');

		$client = new \MailchimpMarketing\ApiClient();
		$client->setConfig([
		    'apiKey' => $api_token,
			'server' => $mailchimp_server
		]);

		// Get variables from new_queue


		    $deal_title = $new_queue->name;
		    $email = $new_queue->email;
		    $org = $new_queue->company;
		    $label = $new_queue->label;
		    $fname = $new_queue->first_name;
		    $lname = $new_queue->last_name;
		    $tags = $new_queue->addedtags;
		    ddl($tags);
		    if ($label == '5') {
		    	$labeltext = 'Customer';
		    } else if ($label == '6') {
		    	$labeltext = 'Hot Lead';
		    } else if ($label == '7') {
		    	$labeltext = 'Warm Lead';
		    } else if ($label == '8') {
		    	$labeltext = 'Cold Lead';
		    }
		    
			$subscriberHash = md5(strtolower($email));
			ddl($deal_title . ' ' . $email);
			
			if (($org != "NULL") && ($org != "undefined") && ($org != "") && (empty($org) != TRUE)) {
				$orgresponse = $client->lists->setListMember($mailchimp_listid, $subscriberHash, [
				    "email_address" => $email,
				    "status_if_new" => "subscribed",
				    "merge_fields" => [
				    	"COMPANY" => $org,
				    ]
				]);
			}
			if (($fname != "NULL") && ($fname != "undefined") && ($fname != "") && (empty($fname) != TRUE)) {
				$fnameresponse = $client->lists->setListMember($mailchimp_listid, $subscriberHash, [
				    "email_address" => $email,
				    "status_if_new" => "subscribed",
				    "merge_fields" => [
				    	"FNAME" => $fname,
				    ]
				]);
			}
			if (($lname != "NULL") && ($lname != "undefined") && ($lname != "") && (empty($lname) != TRUE)) {
				$lnameresponse = $client->lists->setListMember($mailchimp_listid, $subscriberHash, [
				    "email_address" => $email,
				    "status_if_new" => "subscribed",
				    "merge_fields" => [
				    	"LNAME" => $lname,
				    ]
				]);
			}
			
		
			if (($tags != "NULL") && ($tags != "undefined") && ($tags != "") && (empty($tags) != TRUE)) {
				ddl($deal_title . ' ' . $email . 'tags section');
				$tagresponse = $client->lists->updateListMemberTags($mailchimp_listid, $subscriberHash, [
	    			"tags" => $tags,
				]);
			
			}
		
		

		
	

	}
}



?>