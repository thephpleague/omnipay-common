## Upgrade apps from 2.x to 3.x
 - The `redirect()` method no calls `exit()` after sending the content. This is up to the developer now.

## Upgrade Gateways from 2.x to 3.x

The primary difference is the HTTP Client. We are now using HTTPlug (http://httplug.io/) but rely on our own interface.

### Breaking
- Change typehint from Guzzle ClientInterface to `Omnipay\Common\Http\Client`
- `$client->get('..')`/`$client->post('..')` will send the request directly, no longer need to call `->send()`.
- Instead of `$client->createRequest(..)->send()`, you should call `$client->sendRequest($client->createRequest(..))`.
- When sending an AJAX body, convert the body to a string with `json_encode`.
- The response is a PSR-7 Response object. You can call `$response->getBody()->getContents()` to get the body as string.
- `$response->json()` and `$response->xml()` are gone, but `Omnipay\Common\Http\ResponseParser::json($response)` 
and  `Omnipay\Common\Http\ResponseParser::xml($response)` can be used instead.
- An HTTP Client is no longer added by default by `omnipay/common`, but `omnipay/omnipay` will add Guzzle. 
Gateways should not rely on Guzzle or other clients directly.

### Deprecated
- The `$headers` parameters should be an `array` (not `null`, but can be empty)
- `$body` should be a string (eg. `http_build_query($data)` or `json_encode($data)` instead of just $data.
