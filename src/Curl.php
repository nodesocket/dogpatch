<?php
namespace Dogpatch;

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

class Curl {
    private $curlObject;

    protected function __construct($username = null, $password = null, $timeout = 60, $sslVerifyPeer = true, $verbose = false) {
        $this->curlObject = curl_init();

        curl_setopt($this->curlObject, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($this->curlObject, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlObject, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curlObject, CURLOPT_MAXREDIRS, 10);
        curl_setopt($this->curlObject, CURLOPT_HEADER, 1);

        if ($sslVerifyPeer) {
            curl_setopt($this->curlObject, CURLOPT_CAINFO, __DIR__ . '/assets/ssl/ca-bundle.crt');
            curl_setopt($this->curlObject, CURLOPT_SSL_VERIFYPEER, true);
        } else {
            curl_setopt($this->curlObject, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_setopt($this->curlObject, CURLOPT_USERAGENT, "dogpatch");

        if (!empty($username) || !empty($password)) {
            curl_setopt($this->curlObject, CURLOPT_USERPWD, $username . ":" . $password);
            curl_setopt($this->curlObject, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        if ($verbose) {
            if (!file_exists(__DIR__ . "/logs")) {
                mkdir(__DIR__ . "/logs", 0775);
            }

            curl_setopt($this->curlObject, CURLOPT_STDERR, fopen(__DIR__ . "/logs/curl_debug.log", "a+"));
            curl_setopt($this->curlObject, CURLOPT_VERBOSE, true);
        }
    }

    protected function getRequest($url, array $headers = array()) {
        curl_setopt($this->curlObject, CURLOPT_URL, $url);
        curl_setopt($this->curlObject, CURLOPT_POST, false);
        curl_setopt($this->curlObject, CURLOPT_CUSTOMREQUEST, 'GET');

        if (!empty($headers)) {
            curl_setopt($this->curlObject, CURLOPT_HTTPHEADER, $headers);
        }

        return curl_exec($this->curlObject);
    }

    protected function postRequest($url, array $postData = array(), array $headers = array()) {
        curl_setopt($this->curlObject, CURLOPT_URL, $url);
        curl_setopt($this->curlObject, CURLOPT_POST, true);
        curl_setopt($this->curlObject, CURLOPT_CUSTOMREQUEST, 'POST');

        if (!empty($headers)) {
            curl_setopt($this->curlObject, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($this->curlObject, CURLOPT_POSTFIELDS, $postData);

        return curl_exec($this->curlObject);
    }

    protected function putRequest($url, array $headers = array()) {
        curl_setopt($this->curlObject, CURLOPT_URL, $url);
        curl_setopt($this->curlObject, CURLOPT_POST, false);
        curl_setopt($this->curlObject, CURLOPT_CUSTOMREQUEST, 'PUT');

        if (!empty($headers)) {
            curl_setopt($this->curlObject, CURLOPT_HTTPHEADER, $headers);
        }

        return curl_exec($this->curlObject);
    }

    protected function deleteRequest($url, array $headers = array()) {
        curl_setopt($this->curlObject, CURLOPT_URL, $url);
        curl_setopt($this->curlObject, CURLOPT_POST, false);
        curl_setopt($this->curlObject, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if (!empty($headers)) {
            curl_setopt($this->curlObject, CURLOPT_HTTPHEADER, $headers);
        }

        return curl_exec($this->curlObject);
    }

    protected function headRequest($url, array $headers = array()) {
        curl_setopt($this->curlObject, CURLOPT_URL, $url);
        curl_setopt($this->curlObject, CURLOPT_POST, false);
        curl_setopt($this->curlObject, CURLOPT_CUSTOMREQUEST, 'HEAD');

        if (!empty($headers)) {
            curl_setopt($this->curlObject, CURLOPT_HTTPHEADER, $headers);
        }

        return curl_exec($this->curlObject);
    }

    protected function getCurlInfo($curl_option) {
        return curl_getinfo($this->curlObject, $curl_option);
    }

    protected function close() {
        curl_close($this->curlObject);
        unset($this->curlObject);
    }
}

?>
