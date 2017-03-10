Forms Unit Test Profiling
=========================

To generate x amount of random forms, run

```bash
$ bin/console bin/console generate:forms -t {num}
```

where `{num}` is the number of forms to generate (default to 1000).

To execute the tests, just run `phpunit`

-----------------------------------------------------------------------------------------------------------

To run the tests with a standard checkout (as it exists in master currently) check out the branch `test-forms-without-extension`

To run the tests with the validation extension in the forms (which is registered for every form test) check out the branch `test-forms-with-extension`

(Remember to run `composer install` after switching the branches)