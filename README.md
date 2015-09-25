# sosmed-cms
Simple login using Social Media for CMS Enterprise

## Synopsis

When the web want to simplify for login method using social media, how that is very **simple** and **easy** setup. This plugin only support for CMS Enterprise v.2.3.1 and their clients.

PARAMETER:
```
{BASE_DOMAIN} : your domain set or installed the plugin
{PROVIDER}    : facebook | twitter | linkedin | google
```

## Code Example
HTTP Request
```
{BASE_DOMAIN}/api/{PROVIDER}/login/get
```
Result JSON
```
{"status":true,"url":"http:\/\/{BASE_DOMAIN}\/api\/twitter\/callback\/data"}
```

## Tests

```
{BASE_DOMAIN}/api/{PROVIDER}/login/get?test=true
```

## Contributors

Let people know how they can dive into the project, include important links to things like issue trackers, irc, twitter accounts if applicable.

## License

A short snippet describing the license (MIT, Apache, etc.)
