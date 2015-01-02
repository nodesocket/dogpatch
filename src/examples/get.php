<?php
    /*
    # Copyright 2015 NodeSocket, LLC.
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

    require_once(__DIR__ . "/../Curl.php");
    require_once(__DIR__ . "/../Dogpatch.php");

    use Dogpatch\Dogpatch;

    $dogpatch = new Dogpatch();

    $dogpatch->get("https://freegeoip.net/csv/8.8.8.8")
             ->assertStatusCode(200)
             ->assertHeaders(array(
                "Access-Control-Allow-Origin" => "*"
             ))
             ->assertBody('"8.8.8.8","US","United States","","","","","38.0000","-97.0000","",""');

    $dogpatch->get("https://www.google.com")
             ->assertStatusCode(200)
             ->assertHeadersExist(array(
                "X-Frame-Options"
             ))
             ->assertHeaders(array(
                "Server" => "gws",
                "Transfer-Encoding" => "chunked"
             ))
             ->assertBody("/<!doctype html>.*/", USE_REGEX);

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
             ->assertTotalTimeLessThan(2)
             ->close();
