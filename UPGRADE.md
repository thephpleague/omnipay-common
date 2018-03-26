## Upgrade apps from 2.x to 3.x
 - The `redirect()` method no calls `exit()` after sending the content. This is up to the developer now.

## Upgrade Gateways from 2.x to 3.x

The primary difference is the HTTP Client. We are now using HTTPlug (http://httplug.io/) but rely on our own interface.

### Breaking
- Change typehint from Guzzle ClientInterface to `Omnipay\Common\Http\ClientInterface`
- `$client->get('..')`/`$client->post('..')` etc are removed, you can call `$client->request('GET', '')`.
- No need to call `$request->send()`, requests are sent directly.
- Instead of `$client->createRequest(..)` you can create+send the request directly with `$client->request(..)`.
- When sending a JSON body, convert the body to a string with `json_encode()` and set the correct Content-Type.
- The response is a PSR-7 Response object. You can call `$response->getBody()->getContents()` to get the body as string.
- `$response->json()` and `$response->xml()` are gone, but you can implement the logic directly.
- An HTTP Client is no longer added by default by `omnipay/common`, but `omnipay/omnipay` will add Guzzle. 
Gateways should not rely on Guzzle or other clients directly.
- `$body` should be a string (eg. `http_build_query($data)` or `json_encode($data)` instead of just `$data`).
- The `$headers` parameters should be an `array` (not `null`, but can be empty)

Note: Prior to stable release, the goal is to migrate to PSR-18, once completed. 
This will not change the gateway implementations.

