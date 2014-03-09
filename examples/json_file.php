<?php
    /*
    # Copyright 2014 NodeSocket, LLC
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

    require_once(dirname(__dir__) . "/Dogpatch.php");

    $dogpatch = new Dogpatch();

    $dogpatch->get("https://freegeoip.net/json/8.8.8.8")
             ->assert_status_code(200)
             ->assert_headers_exist(array(
                "Content-Length"
             ))
             ->assert_headers(array(
                "Access-Control-Allow-Origin" => "*"
             ))
             ->assert_body_json_file(dirname(__DIR__) . "/examples/json/freegeoip.net.json", ECHO_JSON)
             ->close();
?>