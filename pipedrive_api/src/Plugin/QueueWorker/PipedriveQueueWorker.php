<?php  
/**
 * @file
 * Contains \Drupal\pipedrive_api\Plugin\QueueWorker\PipedriveQueueWorker.
 */

namespace Drupal\pipedrive_api\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\pipedrive_api\MailchimpPush;

/**
 * Processes tasks for example module.
 *
 * @QueueWorker(
 *   id = "pipedrive_queue",
 *   title = @Translation("Pipedrive Module Queue worker"),
 *   cron = {"time" = 90}
 * )
 */
class PipedriveQueueWorker extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($item) {

  	$mailchimp = new MailchimpPush();
	  $mailchimp->add_to_audience($item);
  }

}