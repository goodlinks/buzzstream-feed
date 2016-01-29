# BuzzStream Feed

Show your activity feed to clients or to the world.

BuzzStream Feed is brought to you by <a href="http://goodlinks.io">GoodLinks - The Content Outreach Service</a>

## Overview

BuzzStream Feed pulls your activity out of your BuzzStream account in order to share it with others.  There are two primary modes that it can be used for:

1. For the World - Show your activity to the world with personal data fully scrubbed.  All they can see is the reputation level and relationship status of the people you're reaching out to.
2. For Clients - Show clients your outreach activity.  They can see the names / subject lines / dates, but they can't see contents of the emails for privacy reasons.

## Usage

You'll want to pull the package into your project:

```json
{
  "require": {
      "goodlinks/buzzstream-feed": "dev-master"
  },
  "repositories": [
      {
          "type": "vcs",
          "url": "git@github.com:goodlinks/buzzstream-feed.git"
      }
  ]
}
```
And then from within your app, you can grab your history and display it like this:

```php
Api::setConsumerKey($consumerKey);
Api::setConsumerSecret($consumerSecret);

$history = History::getList();

foreach ($history as $historyItem) {
    $date = $historyItem->getDate();
    $websiteUrls = $historyItem->getWebsiteNamesCsv();
    $project = $historyItem->getProjectName();
    
    echo "$project - $date: $websiteUrls";
}
```

By default, API requests will be cached for 24 hours

## License

The license is currently 
<a href="https://tldrlegal.com/license/creative-commons-attribution-noncommercial-(cc-nc)#summary">Creative Commons Attribution NonCommercial</a>. 

TL;DR is that you can modify and distribute but not for commercial use.  Most likely if you have an
interesting idea for commercial use, I'll be fine with it - just get in touch.  

So long as you're not
just copying and pasting this and reselling it.
