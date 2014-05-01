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

    $dogpatch = new Dogpatch();

    $dogpatch->get("https://freegeoip.net/json/8.8.8.8")
             ->assertStatusCode(200)
             ->assertHeadersExist(array(
                "Content-Length"
             ))
             ->assertHeaders(array(
                "Access-Control-Allow-Origin" => "*"
             ))
             ->assertBodyJsonFile(__DIR__ . "/json/freegeoip.net.json", PRINT_JSON)
             ->close();
?>
