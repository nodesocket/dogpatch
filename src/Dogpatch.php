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

require_once(__DIR__ . '/Util.php');

define("IS_VALID_JSON", "IS_VALID_JSON");
define("IS_EMPTY", "IS_EMPTY");
define("USE_REGEX", true);
define("DONT_USE_REGEX", false);
define("VAR_EXPORT", true);
define("DONT_VAR_EXPORT", false);
define("PRINT_JSON", true);
define("DONT_PRINT_JSON", false);

class Dogpatch extends Curl {
    private $response;
    private $statusCode;
    private $headers;
    private $body;

    public function __construct(array $curlOptions = array()) {
        parent::__construct($curlOptions);
    }

    public function get($url, array $headers = array()) {
        $this->unsetClassVars();
        $this->response = $this->getRequest($url, $headers);

        return $this;
    }

    public function post($url, array $postData = array(), array $headers = array()) {
        $this->unsetClassVars();
        $this->response = $this->postRequest($url, $postData, $headers);

        return $this;
    }

    public function put($url, array $headers = array()) {
        $this->unsetClassVars();
        $this->response = $this->putRequest($url, $headers);

        return $this;
    }

    public function delete($url, array $headers = array()) {
        $this->unsetClassVars();
        $this->response = $this->deleteRequest($url, $headers);

        return $this;
    }

    public function head($url, array $headers = array()) {
        $this->unsetClassVars();
        $this->response = $this->headRequest($url, $headers);

        return $this;
    }

    public function assertStatusCode($assertedStausCode) {
        if (empty($this->statusCode)) {
            $this->statusCode = $this->getCurlInfo(CURLINFO_HTTP_CODE);
        }

        if (intval($assertedStausCode) !== intval($this->statusCode)) {
            throw new \Exception("Asserted status code '$assertedStausCode' does not equal response status code '$this->statusCode'.");
        }

        return $this;
    }

    public function assertHeadersExist(array $assertedHeaders = array()) {
        if (empty($this->headers)) {
            $headersRaw = substr($this->response, 0, $this->getCurlInfo(CURLINFO_HEADER_SIZE));
            $this->headers = array_change_key_case(http_parse_headers($headersRaw), CASE_LOWER);
        }

        $assertedHeaders = array_map('strtolower', $assertedHeaders);

        foreach ($assertedHeaders as $header) {
            if (!isset($this->headers[$header])) {
                throw new \Exception("Asserted header '$header' is not set.");
            }
        }

        return $this;
    }

    public function assertHeaders(array $assertedHeaders = array()) {
        if (empty($this->headers)) {
            $headersRaw = substr($this->response, 0, $this->getCurlInfo(CURLINFO_HEADER_SIZE));
            $this->headers = array_change_key_case(http_parse_headers($headersRaw), CASE_LOWER);
        }

        ////
        // Associated array
        ////
        if (is_assoc($assertedHeaders)) {
            $assertedHeaders = array_change_key_case($assertedHeaders, CASE_LOWER);

            foreach ($assertedHeaders as $k => $v) {
                if (!array_key_exists($k, $this->headers)) {
                    throw new \Exception("Asserted header '$k' is not set.");
                }

                if (is_array($this->headers[$k])) {
                    if (!in_array($v, $this->headers[$k])) {
                        throw new \Exception("Asserted header '$k' exists, but the response header value '$v' is not equal.");
                    }
                } else {
                    if ($v !== $this->headers[$k]) {
                        throw new \Exception("Asserted header '$k=$v' does not equal response header '$k=" . $this->headers[$k] . "'.");
                    }
                }
            }
        }
        ////
        // Standard indexed array, call assertHeadersExist() instead
        ////
        else {
            $this->assertHeadersExist($assertedHeaders);
        }

        return $this;
    }

    public function assertBody($assertedBody, $useRegularExpression = false) {
        if (empty($this->body)) {
            $this->body = substr($this->response, $this->getCurlInfo(CURLINFO_HEADER_SIZE));
        }

        if ($assertedBody === IS_EMPTY) {
            if ($this->body === false || $this->body === "") {
                return $this;
            } else {
                throw new \Exception("Response body is not empty.");
            }
        }

        if ($assertedBody === IS_VALID_JSON) {
            if (json_decode($this->body === null)) {
                throw new \Exception("Response body is invalid JSON.");
            }

            return $this;
        }

        if ($useRegularExpression) {
            if (!@preg_match($assertedBody, $this->body)) {
                throw new \Exception("Asserted body '$assertedBody' does not match response body of '$this->body'.");
            }
        } else {
            if (strpos($assertedBody, $this->body)) {
                throw new \Exception("Asserted body '$assertedBody' does not equal response body of '$this->body'.");
            }
        }

        return $this;
    }

    public function assertBodyPhp($asserted, $onNotEqualVarExport = false) {
        if (empty($this->body)) {
            $this->body = substr($this->response, $this->getCurlInfo(CURLINFO_HEADER_SIZE));
        }

        $body = json_decode($this->body);

        if ($body === null) {
            throw new \Exception("Response body is invalid JSON.");
        }

        if ($asserted != $body) {
            $errorMessage = "Asserted body does not equal response body.";
            if ($onNotEqualVarExport) {
                $errorMessage .= "\n\n--------------- ASSERTED BODY ---------------\n" . var_export($asserted, true) .
                                 "\n\n--------------- RESPONSE BODY ---------------\n" . var_export($body, true) . "\n\n";
            }
            throw new \Exception($errorMessage);
        }

        return $this;
    }

    public function assertBodyJsonFile($assertedJsonFile, $onNotEqualPrintJson = false) {
        if (!file_exists($assertedJsonFile)) {
            throw new \Exception("Asserted JSON file '$assertedJsonFile' does not exist.");
        }

        $asserted = file_get_contents($assertedJsonFile);
        if (json_decode($asserted) === null) {
            throw new \Exception("Asserted JSON file is invalid JSON.");
        }

        if (empty($this->body)) {
            $this->body = substr($this->response, $this->getCurlInfo(CURLINFO_HEADER_SIZE));
        }

        if (json_decode($this->body) === null) {
            throw new \Exception("Response body is invalid JSON.");
        }

        $asserted = pretty_print_json($asserted);
        $body = pretty_print_json($this->body);

        if ($asserted != $body) {
            $errorMessage = "Asserted JSON file does not equal response body.";
            if ($onNotEqualPrintJson) {
                $errorMessage .= "\n\n--------------- ASSERTED JSON FILE ---------------\n" . $asserted .
                                 "\n\n--------------- RESPONSE BODY ---------------\n" . $body . "\n\n";
            }
            throw new \Exception($errorMessage);
        }

        return $this;
    }

    public function close() {
        parent::close();
        $this->unsetClassVars();
    }

    private function unsetClassVars() {
        unset($this->response);
        unset($this->statusCode);
        unset($this->headers);
        unset($this->body);
    }
}
