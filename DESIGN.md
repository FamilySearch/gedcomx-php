## Testing

Testing is done with [PHPUnit](https://phpunit.de/) as the test runner. Tests are
organized into suites in the `tests/Functional` directory. Test suites extend
`ApiTestCase` which provides helpful methods such as creating a client object.

The current tests are all functional tests. That is why code coverage is so low.
This library provides objects for the full Gedcomx model but the FamilySearch API
does not support the full model. The functional tests focus on testing what the
FamilySearch API supports.

Unit tests should be added.

For authentication, tests use a client_id that is granted special permission to
use the OAuth2 password flow so that we can authenticate programmatically. The
client_id only functions in sandbox.

Tests are recorded with [PHP-VCR](https://github.com/php-vcr/php-vcr), otherwise
the tests would run for close to an hour. PHP-VCR matches requests from the SDK
to a list of requests stored in JSON file. There is one JSON file for each test.
The `@vcr` annotation specifies which file the requests are found in. The files
are stored in the `tests/fixtures` directory.

PHP-VCR matches intercepted requests against recorded requests to determine which
response should be played back. It can be configured to use any method for comparison.
At the time of writing we are comparing against the request method, url, query params,
body, and all headers except for `User-Agent`.

Because we are comparing request bodies, we must gaurantee that any generated
data is always the same. We use [Faker](https://github.com/fzaninotto/Faker) and
seed it to gaurantee the data is always the same. When writing new tests which
generate data, be sure to use Faker. An instance of faker is available in all
test suites via `$this->faker`.

Using PHP-VCR also means a given test cannot issue two requests with the same
method-url-query-body-headers combination and expect different results. We want
to test different responses to the same URL when verifying that updates to an
object work. The easier way to get around this limitation is to try appending a 
useless query parameter to the url.

Some tests which are not easily recorded may run live. Tests for most memories 
endpoints are skipped because PHP-VCR does not record files
in the post body well and the endpoints for live testing are unreliable.

PHP-VCR is configured in `tests/bootstrap.php`.