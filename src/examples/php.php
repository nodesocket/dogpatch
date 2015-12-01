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

    $expected = new stdClass();
    $expected->ip = "8.8.8.8";
    $expected->country_code = "US";
    $expected->country_name = "United States";
    $expected->region_code = "CA";
    $expected->region_name = "California";
    $expected->city = "Mountain View";
    $expected->zip_code = "94040";
    $expected->time_zone = "America/Los_Angeles";
    $expected->latitude = 37.386000000000003;
    $expected->longitude = -122.0838;
    $expected->metro_code = 807;

    $dogpatch = new Dogpatch();

    $dogpatch->get("https://freegeoip.net/json/8.8.8.8")
             ->assertStatusCode(200)
             ->assertHeadersExist(array(
                "Date"
             ))
             ->assertHeaders(array(
                "Access-Control-Allow-Origin" => "*"
             ))
             ->assertBodyPhp($expected, VAR_EXPORT)
             ->close();
