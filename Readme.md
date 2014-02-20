# Famelo.Messaging

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