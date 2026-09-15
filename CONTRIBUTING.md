# Contributing

Contributions are welcome, via pull request against the `main` branch.

## Reporting bugs

Please raise an issue with:

- SilverStripe framework/CMS version
- Steps to reproduce
- Expected vs. actual behaviour

## Pull requests

- Keep changes focused — one feature/fix per PR.
- Follow the existing code style ([PSR-12](https://www.php-fig.org/psr/psr-12/); run
  `composer phpcs` if you have `require-dev` installed).
- Add or update tests under `tests/` where practical.
- Update `CHANGELOG.md` under an `[Unreleased]` heading.

## Development setup

This module isn't a runnable SilverStripe project on its own. To develop against it, require it
as a path repository from a local SilverStripe installation:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../silverstripe-before-after"
        }
    ],
    "require": {
        "normann/silverstripe-before-after": "*"
    }
}
```

then `composer update normann/silverstripe-before-after` and `dev/build flush=1`.
