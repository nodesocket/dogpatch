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

    require_once(dirname(__DIR__) . "/Dogpatch.php");

    $dogpatch = new Dogpatch();

    $dogpatch->get("https://www.google.com")
             ->assert_status_code(200)
             ->assert_headers_exist(array(
                "X-Frame-Options"
             ))
             ->assert_headers(array(
                "Server" => "gws"
             ))
             ->assert_body("/<!doctype html>.*/")
             ->close();
?>