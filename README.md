# Sencivity
A CiviCRM extension to send various events and metrics to sensu. 
At the moment, the only feature is the publication of scheduled job execution results.

## Scheduled job execution results
This feature relies on the hooks added for [CRM-21460](https://issues.civicrm.org/jira/browse/CRM-21460). 
To use this feature, set-up a check called `civicrm_jobs` (it must be this name) in your Sensu server. 
Don't schedule the check (and therefore the command does not matter), and disable auto-resolving 
(so that succeeding jobs don't hide previous failed jobs). For example:

```
{
  "checks": {
    "civicrm_jobs": {
      "command": "echo OK",
      "publish": false,
      "auto_resolve": false
    }
  }
}
```

Then install this extension, and go to https://your.civi.domain/civicrm/sencivity/settings 
and set the root URL of the API for your Sensu server, and a sensu client name. 
The default client name is fine (it doesn't need to be pre-declared in Sensu), but you may want 
to change it to an existing client of your infrastructure, and/or you may to use different client
names for different Civi environments (test, prod, etc).

Once these settings are saved, you should be able to see the execution result of jobs in Sensu.

## Stripe errors
This feature uses the fatal error handler to send a Sensu warning whenever a fatal error occurs 
and the error message contains "stripe" (regardless of case). The Sensu check is called
`civicrm_stripe_webhook` and the procedure to use this feature is the same as for the job execution 
results above.
