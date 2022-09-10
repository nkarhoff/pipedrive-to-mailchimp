# PipeDrive to Mailchimp

This is a custom Drupal module that takes contacts in Pipedrive and imports them into Mailchimp. 

# Dependencies
- Pipedrive account with API key
- Mailchimp account

# Details
Client API keys are entered on the /admin/config/development/pipedrive-api page on your Drupal site. 
When cron runs, contacts are pulled from Pipedrive and checked for changes. Contacts that have had changes since the last import are added to the queue. 
The QueueWorker initiates the push to Mailchimp.
