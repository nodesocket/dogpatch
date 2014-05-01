<?php
    /*
    # Copyright 2014 NodeSocket, LLC.
    #
    # Licensed under the Apache License, Version 2.0 (the "License");
    # you may not use this file except in compliance with the License.
    # You may obtain a copy of the License at
    #
    # http://www.apache.org/licenses/LICENSE-2.0
    #
    # Unless required by applicable law or agreed to in writing, software
    # distributed under the License is distributed on an "AS IS" BASIS,
    # WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    # See the License for the specific language governing permissions and
    # limitations under the License.
    */

    require_once(__DIR__ . "/../Util.php");
    require_once(__DIR__ . "/../Curl.php");
    require_once(__DIR__ . "/../Dogpatch.php");

    use Dogpatch\Dogpatch;

    $dogpatch = new Dogpatch(array(
        "username" => "foo",
        "password" => 'bar',
        "timeout" => 10
    ));

    $dogpatch->get("https://api.stripe.com")
             ->assertStatusCode(401)
             ->assertHeadersExist(array(
                "www-Authenticate"
             ))
             ->assertHeaders(array(
                "Server" => "nginx",
                "Cache-Control" => "no-cache, no-store"
             ))
             ->assertBody(IS_VALID_JSON)
             ->close();
?>
