# Famelo.Messaging

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mneuhaus/Famelo.Messaging/badges/quality-score.png?s=a27fa967fe33de193aac7e2846400286578f5dfb)](https://scrutinizer-ci.com/g/mneuhaus/Famelo.Messaging/)

[![Build Status](https://travis-ci.org/mneuhaus/Famelo.Messaging.png?branch=master)](https://travis-ci.org/mneuhaus/Famelo.Messaging)

[![License](https://poser.pugx.org/famelo/messaging/license.png)](https://packagist.org/packages/famelo/messaging)

[![Total Downloads](https://poser.pugx.org/famelo/messaging/downloads.png)](https://packagist.org/packages/famelo/messaging)

Little helper to send Messages with SwiftMailer rendered through Fluid:

```php
  	$mail = new \Famelo\Messaging\Message();
	$mail->setFrom(array('mail@me.com' => 'Me :)'))
		->setTo(array('mail@you.com'))
		->setSubject('How are you?')
		->setMessage('My.Package:HelloWorld')
		->assign('someVariable', 'Hello World')
		->send();
````

This will try to render a Template at resource://My.Package/Private/Messages/HelloWorld.html

## Configuration Example

```yaml
Famelo:
  Messaging:
    defaultFrom:
      mneuhaus@famelo.com: 'Marc Neuhaus'

  	# Redirect all messages to this E-Mail
  	# for testing purposes
    redirectAllMessagesTo: foo@bar.com

TYPO3:
  SwiftMailer:
    transport:
      type: 'Swift_SmtpTransport'
      options:
        host: 'mail.foo.com'
        username: 'foo'
        password: 'bar'
```
