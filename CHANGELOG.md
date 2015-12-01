CHANGELOG
=========

## V1.1.3 - *12/01/2015*

- Fixes typo in `assertBody()`. Thanks [@pmcneill](https://github.com/pmcneill)

## V1.1.2 - *12/29/2014*

- Added `assertTotalTimeLessThan()` assertion. Thanks [@dvhirt](https://github.com/dvhirt)

## V1.1.1 - *9/8/2014*

- Updated `ca-bundle.crt` to the latest version.

## v1.1.0 - *9/2/2014*
- Fixed bug in Curl.php. It was always setting CURLOPT_POST to false, which would not send POST requests.

### v1.0.0 - *5/6/2014*
- Composer support
- PSR-4 compliant
- Refactored/renamed using camelCase
- Fixed examples. Freegeoip.net renamed response property `areacode` to `area_code`.

### v0.1.3 - *3/9/2014*
- Fixed serious bug in **Curl.php**. Was not setting curl option `CURLOPT_CUSTOMREQUEST` back to `GET` and `POST` in `get_request()` and `post_request()`.

### v0.1.2 - *3/8/2014*
- Fixed serious bug in **Curl.php**. Was not setting curl option `CURLOPT_POST` back to `false` in `get_request()`.

### v0.1.1 - *3/8/2014*
- Added a special flag `IS_EMPTY` which can be passed into `assertBody()` to assert an empty response body.
