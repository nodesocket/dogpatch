dogpatch
========

![dogpatch](http://blog.preservationnation.org/wp-content/uploads/2012/01/Dogpatch-Historic-District.jpg)

#### An API testing framework, written in PHP using curl. Supports https, basic authentication, passing custom request headers, and most request methods. Orginally written for [Commando.io](https://commando.io).

#### See complete examples in https://github.com/commando/dogpatch/tree/master/examples.

Requirements
------------

#### PHP ####
Version **5.3.0** or greater.

#### PHP Extensions ####
+ **curl**

Constructor
-----------

    $dogpatch = new Dogpatch(array $options = array());

##### Options

>**username:** A basic authentication username. Defaults to `null`.

>**password:** A basic authentication password. Defaults to `null`.

>**timeout:** Curl http request timeout in seconds. Defaults to `60`.

>**ssl_verifypeer:** Attempt to verify ssl peer certificates using included `ca-bundle.crt`. Defaults to `true`.

>**verbose:** Turns on verbose curl logging, and logs all requests into a file in `logs/curl_debug.log`. Defaults to `false`.

Get
---

    $dogpatch->get($url, array $headers = array());

##### Parameters

>**url:** A compete url including the sheme *(http, https)*.

>**headers:** An associated array of additional request headers to pass. Defaults to an empty array.

Post
----

    $dogpatch->post($url, array $post_data = array(), array $headers = array());

##### Parameters

>**url:** A compete url including the sheme *(http, https)*.

>**post_data:** An associated arrray of post data in `key => value` syntax.

>**headers:** An associated array of additional request headers to pass. Defaults to an empty array.

Put
---

    $dogpatch->put($url, array $headers = array());

##### Parameters

>**url:** A compete url including the sheme *(http, https)*.

>**headers:** An associated array of additional request headers to pass. Defaults to an empty array.

Delete
------

    $dogpatch->delete($url, array $headers = array());

##### Parameters

>**url:** A compete url including the sheme *(http, https)*.

>**headers:** An associated array of additional request headers to pass. Defaults to an empty array.

Head
----

    $dogpatch->head($url, array $headers = array());

##### Parameters

>**url:** A compete url including the sheme *(http, https)*.

>**headers:** An associated array of additional request headers to pass. Defaults to an empty array.

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

Copyright 2014 NodeSocket, LLC

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this work except in compliance with the License. You may obtain a copy of the License in the LICENSE file, or at:

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.