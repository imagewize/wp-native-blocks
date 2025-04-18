# Acorn Sage Native Block Package

This repo can be used to scaffold an Acorn package. See the [Acorn Package Development](https://roots.io/acorn/docs/package-development/) docs for further information.

## Installation

You can install this package with Composer:

```bash
composer require imagewizee/sage-native-block-package
```

You can publish the config file with:

```shell
$ wp acorn vendor:publish --provider="Imagewize\SageNativeBlockPackage\Providers\SageNativeBlockServiceProvider"
```

## Usage

From a Blade template:

```blade
@include('SageNativeBlock::sage-native-block')
```

From WP-CLI:

```shell
$ wp acorn sage-native-block
```
