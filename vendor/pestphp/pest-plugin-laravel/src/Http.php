<?php

declare(strict_types=1);

namespace Pest\Laravel;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;

/**
 * Define additional headers to be sent with the request.
 *
 * @return TestCase
 */
function withHeaders(array $headers)
{
    return test()->withHeaders(...func_get_args());
}

/**
 * Add a header to be sent with the request.
 *
 * @return TestCase
 */
function withHeader(string $name, string $value)
{
    return test()->withHeader(...func_get_args());
}

/**
 * Flush all the configured headers.
 *
 * @return TestCase
 */
function flushHeaders()
{
    return test()->flushHeaders(...func_get_args());
}

/**
 * Define a set of server variables to be sent with the requests.
 *
 * @return TestCase
 */
function withServerVariables(array $server)
{
    return test()->withServerVariables(...func_get_args());
}

/**
 * Disable middleware for the test.
 *
 * @param string|array|null $middleware
 *
 * @return TestCase
 */
function withoutMiddleware($middleware = null)
{
    return test()->withoutMiddleware(...func_get_args());
}

/**
 * Enable the given middleware for the test.
 *
 * @param string|array|null $middleware
 *
 * @return TestCase
 */
function withMiddleware($middleware = null)
{
    return test()->withMiddleware(...func_get_args());
}

/**
 * Define additional cookies to be sent with the request.
 *
 * @return TestCase
 */
function withCookies(array $cookies)
{
    return test()->withCookies(...func_get_args());
}

/**
 * Add a cookie to be sent with the request.
 *
 * @return TestCase
 */
function withCookie(string $name, string $value)
{
    return test()->withCookie(...func_get_args());
}

/**
 * Define additional cookies will not be encrypted before sending with the request.
 *
 * @return TestCase
 */
function withUnencryptedCookies(array $cookies)
{
    return test()->withUnencryptedCookies(...func_get_args());
}

/**
 * Add a cookie will not be encrypted before sending with the request.
 *
 * @return TestCase
 */
function withUnencryptedCookie(string $name, string $value)
{
    return test()->withUnencryptedCookie(...func_get_args());
}

/**
 * Automatically follow any redirects returned from the response.
 *
 * @return TestCase
 */
function followingRedirects()
{
    return test()->followingRedirects(...func_get_args());
}

/**
 * Disable automatic encryption of cookie values.
 *
 * @return TestCase
 */
function disableCookieEncryption()
{
    return test()->disableCookieEncryption(...func_get_args());
}

/**
 * Set the referer header and previous URL session value in order to simulate a previous request.
 *
 * @return TestCase
 */
function from(string $url)
{
    return test()->from(...func_get_args());
}

/**
 * Visit the given URI with a GET request.
 *
 * @return TestResponse
 */
function get(string $uri, array $headers = [])
{
    return test()->get(...func_get_args());
}

/**
 * Visit the given URI with a GET request, expecting a JSON response.
 *
 * @return TestResponse
 */
function getJson(string $uri, array $headers = [])
{
    return test()->getJson(...func_get_args());
}

/**
 * Visit the given URI with a POST request.
 *
 * @return TestResponse
 */
function post(string $uri, array $data = [], array $headers = [])
{
    return test()->post(...func_get_args());
}

/**
 * Visit the given URI with a POST request, expecting a JSON response.
 *
 * @return TestResponse
 */
function postJson(string $uri, array $data = [], array $headers = [])
{
    return test()->postJson(...func_get_args());
}

/**
 * Visit the given URI with a PUT request.
 *
 * @return TestResponse
 */
function put(string $uri, array $data = [], array $headers = [])
{
    return test()->put(...func_get_args());
}

/**
 * Visit the given URI with a PUT request, expecting a JSON response.
 *
 * @return TestResponse
 */
function putJson(string $uri, array $data = [], array $headers = [])
{
    return test()->putJson(...func_get_args());
}

/**
 * Visit the given URI with a PATCH request.
 *
 * @return TestResponse
 */
function patch(string $uri, array $data = [], array $headers = [])
{
    return test()->patch(...func_get_args());
}

/**
 * Visit the given URI with a PATCH request, expecting a JSON response.
 *
 * @return TestResponse
 */
function patchJson(string $uri, array $data = [], array $headers = [])
{
    return test()->patchJson(...func_get_args());
}

/**
 * Visit the given URI with a DELETE request.
 *
 * @return TestResponse
 */
function delete(string $uri, array $data = [], array $headers = [])
{
    return test()->delete(...func_get_args());
}

/**
 * Visit the given URI with a DELETE request, expecting a JSON response.
 *
 * @return TestResponse
 */
function deleteJson(string $uri, array $data = [], array $headers = [])
{
    return test()->deleteJson(...func_get_args());
}

/**
 * Visit the given URI with a OPTIONS request.
 *
 * @return TestResponse
 */
function options(string $uri, array $data = [], array $headers = [])
{
    return test()->options(...func_get_args());
}

/**
 * Visit the given URI with a OPTIONS request, expecting a JSON response.
 *
 * @return TestResponse
 */
function optionsJson(string $uri, array $data = [], array $headers = [])
{
    return test()->optionsJson(...func_get_args());
}

/**
 * Call the given URI with a JSON request.
 *
 * @return TestResponse
 */
function json(string $method, string $uri, array $data = [], array $headers = [])
{
    return test()->json(...func_get_args());
}

/**
 * Call the given URI and return the Response.
 *
 * @param string      $method
 * @param string      $uri
 * @param array       $parameters
 * @param array       $cookies
 * @param array       $files
 * @param array       $server
 * @param string|null $content
 *
 * @return TestResponse
 */
function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
{
    return test()->call(...func_get_args());
}
