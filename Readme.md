# Famelo.Messaging

Little helper to send Messages with SwiftMailer rendered through Fluid:

```php
  	$mail = new \Famelo\Messaging\Message();
		$mail
			->setFrom(array('apocalip@gmail.com' => 'Famelo.PackageCatalog'))
			->setTo(array('apocalip@gmail.com'))
			->setSubject('ADU: Übersicht über nicht zufriedene Kunden')
			->setMessage('Famelo.ADU:RatingNeglect')
			->send();
````

This will try to render a Template at resource://Famelo.ADU/Private/Messages/RatingNeglect