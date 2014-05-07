dogpatch
========

![dogpatch](https://raw.github.com/commando/dogpatch/master/src/assets/images/dogpatch.jpg)

#### An HTTP API testing framework, written in PHP using curl. Supports ssl, basic auth, passing custom request headers, redirection *(10 levels)*, and most HTTP request methods. Orginally written for testing the [Commando.io](https://commando.io) API.

##### Canonical Examples

````php
$dogpatch = new Dogpatch();

$dogpatch->get("https://api.github.com")
         ->assertStatusCode(200)
         ->assertHeadersExist(array(
            "X-GitHub-Request-Id",
            "ETag"
         ))
         ->assertHeaders(array(
            "Server" => "GitHub.com"
         ))
         ->assertBody(IS_VALID_JSON)
         ->close();
````

````php
$dogpatch = new Dogpatch();

$dogpatch->get("https://freegeoip.net/json/8.8.8.8")
         ->assertStatusCode(200)
         ->assertHeadersExist(array(
            "Content-Length"
         ))
         ->assertHeaders(array(
            "Access-Control-Allow-Origin" => "*"
         ))
         ->assertBodyJsonFile(dirname(__DIR__) . "/examples/json/freegeoip.net.json")
         ->close();
````

#### See the full examples at https://github.com/commando/dogpatch/tree/master/examples.

Requirements
------------

#### PHP ####
Version **5.3.0** or greater.

#### PHP Extensions ####
Curl

Constructor
-----------

````php
$dogpatch = new Dogpatch(array $curlOptions = array());
````

##### Curl Options

>**username:** A basic authentication username. Defaults to `null`.

>**password:** A basic authentication password. Defaults to `null`.

>**timeout:** Curl HTTP request timeout in seconds. Defaults to `60`.

>**ssl_verifypeer:** Attempt to verify ssl peer certificates using included `ca-bundle.crt`. Defaults to `true`.

>**verbose:** Turns on verbose curl logging, and log all requests into a file `logs/curl_debug.log`. Defaults to `false`.

Get
---

````php
$dogpatch->get($url, array $headers = array());
````

##### Parameters

>**url:** A compete url including the scheme *(HTTP, HTTPS)*.

>**headers:** An optional associated array of additional request headers to pass. Defaults to an empty array.

Post
----

````php
$dogpatch->post($url, array $postData = array(), array $headers = array());
````

##### Parameters

>**url:** A compete url including the scheme *(HTTP, HTTPS)*.

>**postData:** An associated array of post data in `key => value` syntax.

>**headers:** An optional associated array of additional request headers to pass. Defaults to an empty array.

Put
---

````php
$dogpatch->put($url, array $headers = array());
````

##### Parameters

>**url:** A compete url including the scheme *(HTTP, HTTPS)*.

>**headers:** An optional associated array of additional request headers to pass. Defaults to an empty array.

Delete
------

````php
$dogpatch->delete($url, array $headers = array());
````

##### Parameters

>**url:** A compete url including the scheme *(HTTP, HTTPS)*.

>**headers:** An optional associated array of additional request headers to pass. Defaults to an empty array.

Head
----

````php
$dogpatch->head($url, array $headers = array());
````

##### Parameters

>**url:** A compete url including the scheme *(HTTP, HTTPS)*.

>**headers:** An optional associated array of additional request headers to pass. Defaults to an empty array.

Assert Status Code
------------------

````php
$dogpatch->assertStatusCode($assertedStatusCode);
````

##### Parameters

>**assertedStatusCode:** An integer representing the expected response status code.

Assert Headers Exist
--------------------

````php
$dogpatch->assertHeadersExist(array $assertedHeaders = array());
````

##### Parameters

>**assertedHeaders:** A standard indexed array of expected response headers. The acutal values of the response headers **are not checked**, only that the header exists. The headers are checked **case-insensitive**.

Assert Headers
--------------

````php
$dogpatch->assertHeaders(array $assertedHeaders = array());
````

##### Parameters

>**assertedHeaders:** An associated array of expected response headers and their expected value. The acutal values of the response headers **are checked**. The headers are checked **case-insensitive** but the header values are checked **case-sensitive**.

Assert Body
-----------

````php
$dogpatch->assertBody($assertedBody, $useRegularExpression = false);
````

##### Parameters

>**assertedBody:** Assert a response body, takes one of four options. A string that is checked **case-sensitive**. A regular expression that is checked according to the defined expression. A speical flag `IS_VALID_JSON` or `IS_EMPTY`. `IS_VALID_JSON` only validates that the response body is proper JSON and able to be decoded. `IS_EMPTY` validates that the response body is empty.

>**useRegularExpression:** An optional true/false flag which you may reference with globals `USE_REGEX` and `DONT_USE_REGEX`. If you wish `$assertedBody` to be checked via regular expression, you must set this parameter to true. Defaults to false.

Assert Body Against PHP
-----------------------

````php
$dogpatch->assertBodyPhp($asserted, $onNotEqualVarExport = false);
```

##### Parameters

>**asserted:** Assert a native PHP type *(usually a PHP object or array)* against the response body. The response body must be valid JSON, which is automatically decoded and compared against `$asserted`. PHP type keys and values are checked **case-sensitive**.

>**onNotEqualVarExport:** An optional true/false flag which you may reference with globals `VAR_EXPORT` and `DONT_VAR_EXPORT`. If a mismatch is detected between `$asserted` and the response body, variable export both making it convenient to find discrepancies. Defaults to false.

Assert Body Against JSON File
-----------------------------

````php
$dogpatch->assertBodyJsonFile($assertedJsonFile, $onNotEqualPrintJson = false);
```

##### Parameters

>**assertedJsonFile:** Assert a JSON file *(the full path)* against the response body. The response body must be valid JSON, which is automatically decoded, pretty printed, and compared against the passed-in JSON file which is also pretty printed. Key names and values are checked **case-sensitive**.

>**onNotEqualPrintJson:** An optional true/false flag which you may reference with globals `PRINT_JSON` and `DONT_PRINT_JSON`. If a mismatch is detected between the JSON file and the response body, print both making it convenient to find discrepancies. Defaults to false.

Close
-----

````php
$dogpatch->close();
````

Closes the curl connection and unsets the curl resource and all dogpatch class variables. Don't call `close()` until you are completely done making requests with the curl connection. Once you call `close()` you must instantiate a new dogpatch object. For example, the following is invalid and will throw an exception:

````php
$dogpatch = new Dogpatch();
$dogpatch->get("https://www.google.com")
         ->close()
         ->get("https://github.com")
````

Instead do the following:

````php
$dogpatch = new Dogpatch();
$dogpatch->get("https://www.google.com")
         ->close();

$dogpatch = new Dogpatch();
$dogpatch->get("https://github.com")
         ->close();
````

Or, even better, if you'd like to reuse the same curl connection and curl options:

````php
$dogpatch = new Dogpatch();
$dogpatch->get("https://www.google.com")
         ->get("https://github.com")
         ->close();
````

Current Version
---------------

https://github.com/commando/dogpatch/blob/master/VERSION

Changelog
---------

https://github.com/commando/dogpatch/blob/master/CHANGELOG.md

Support, Bugs, And Feature Requests
-----------------------------------

Create issues here in GitHub (https://github.com/commando/dogpatch/issues).

Versioning
----------

For transparency and insight into our release cycle, and for striving to maintain backward compatibility, dogpatch will be maintained under the semantic versioning guidelines.

Releases will be numbered with the follow format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

+ Breaking backward compatibility bumps the major (and resets the minor and patch)
+ New additions without breaking backward compatibility bumps the minor (and resets the patch)
+ Bug fixes and misc changes bumps the patch

For more information on semantic versioning, visit http://semver.org/.

License & Legal
---------------

Copyright 2014 NodeSocket, LLC.

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this work except in compliance with the License. You may obtain a copy of the License in the LICENSE file, or at:

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
