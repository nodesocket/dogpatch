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

    require_once(dirname(__dir__) . "/Dogpatch.php");

    $dogpatch = new Dogpatch(array(
        "timeout" => 30
    ));

    $dogpatch->post("https://api.balancedpayments.com/api_keys")
             ->assert_status_code(201)
             ->assert_headers_exist(array(
                "X-Balanced-Host",
                "X-Balanced-Guru"
             ))
             ->assert_headers(array(
                "Content-Type" => "application/json"
             ))
             ->assert_body(IS_VALID_JSON)
             ->close();
?>