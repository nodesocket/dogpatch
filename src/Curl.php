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
    private $curlOptions = array(
        "username" => null,
        "password" => null,
        "timeout" => 60,
        "ssl_verifypeer" => true,
        "verbose" => false
    );

    protected function __construct(array $curlOptions = array()) {

        $this->curlOptions = array_merge($this->curlOptions, $curlOptions);

        $this->curlObject = curl_init();

        curl_setopt($this->curlObject, CURLOPT_CONNECTTIMEOUT, $this->curlOptions['timeout']);
        curl_setopt($this->curlObject, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlObject, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curlObject, CURLOPT_MAXREDIRS, 10);
        curl_setopt($this->curlObject, CURLOPT_HEADER, 1);

        if ($this->curlOptions['ssl_verifypeer']) {
            curl_setopt($this->curlObject, CURLOPT_CAINFO, __DIR__ . '/assets/ssl/ca-bundle.crt');
            curl_setopt($this->curlObject, CURLOPT_SSL_VERIFYPEER, true);
        } else {
            curl_setopt($this->curlObject, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_setopt($this->curlObject, CURLOPT_USERAGENT, "dogpatch");

        if (!empty($this->curlOptions['username']) || !empty($this->curlOptions['password'])) {
            curl_setopt($this->curlObject, CURLOPT_USERPWD, $this->curlOptions['username'] . ":" . $this->curlOptions['password']);
            curl_setopt($this->curlObject, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        if ($this->curlOptions['verbose']) {
            if (!file_exists(__DIR__ . "/logs")) {
                mkdir(__DIR__ . "/logs", 0775);
            }

            curl_setopt($this->curlObject, CURLOPT_STDERR, fopen(__DIR__ . "/logs/curl_debug.log", "a+"));
            curl_setopt($this->curlObject, CURLOPT_VERBOSE, true);
        }
    }

    private function execute($method, $url, array $headers = array()) {
        curl_setopt($this->curlObject, CURLOPT_URL, $url);
        curl_setopt($this->curlObject, CURLOPT_POST, false);
        curl_setopt($this->curlObject, CURLOPT_CUSTOMREQUEST, $method);

        if (!empty($headers)) {
            curl_setopt($this->curlObject, CURLOPT_HTTPHEADER, $headers);
        }

        return curl_exec($this->curlObject);
    }

    protected function getRequest($url, array $headers = array()) {
       return $this->execute('GET', $url, $headers);
    }

    protected function postRequest($url, array $postData = array(), array $headers = array()) {
        curl_setopt($this->curlObject, CURLOPT_POSTFIELDS, $postData);

        return $this->execute('POST', $url, $headers);
    }

    protected function putRequest($url, array $headers = array()) {
        return $this->execute('PUT', $url, $headers);
    }

    protected function deleteRequest($url, array $headers = array()) {
        return $this->execute('DELETE', $url, $headers);
    }

    protected function headRequest($url, array $headers = array()) {
        return $this->execute('HEAD', $url, $headers);
    }

    protected function getCurlInfo($curl_option) {
        return curl_getinfo($this->curlObject, $curl_option);
    }

    protected function close() {
        curl_close($this->curlObject);
        unset($this->curlObject);
    }
}
