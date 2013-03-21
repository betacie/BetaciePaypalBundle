MassPay
=======

If you need to pay an user you can use MassPay, its use is as easy as pie. All you need is to call `Betacie\Bundle\PaypalBundle\Paypal::masspay` by giving 
user paypal email and the amount to pay.

```php
public function withdrawAction()
{
    $response = $this->get('betacie.paypal')->masspay(user@email.tld, 10);

    if ($response->isSuccess()) {
        // Congratulations !
    } 

}
```