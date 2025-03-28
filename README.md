# PHPHP

PHPHP is a self-hosted interpreter of tiny subset of PHP implemented in pure PHP.

## Requirements

- PHP 8.4

## Installation

No runtime dependencies!

## Usage

```bash
php index.php
```

It will output the following:

```
Running on PHP
Running on PHPHP on PHP
Running on PHPHP on PHPHP on PHP
Hello, World!
```

The script, [hello.php](./hello.php), is executed on PHPHP on PHP.

### PHPHP on PHPHP on PHP

Edit this line as the following:

```diff
 if (defined('PHPHP')) {
-    if (PHPHP < 2) {
+    if (PHPHP < 3) {
         echo "Running" . str_repeat(" on PHPHP", PHPHP) . " on PHP\n";
```

Then, execute `index.php`. I recommend that you enable JIT.

```bash
php -d opcache.enable_cli=on -d opcache.jit=on -d opcache.jit_buffer_size=1G index.php
```

It will execute hello world on PHPHP on PHPHP on PHP.

```
Running on PHP
Running on PHPHP on PHP
Running on PHPHP on PHPHP on PHP
Running on PHPHP on PHPHP on PHPHP on PHP
Hello, World!
```

## Talks

[Talk in PHPerKaigi 2025 (in Japanese)](https://fortee.jp/phperkaigi-2025/proposal/ef8480fc-1403-4020-9f24-aca9361f51e4)

## License

See [LICENSE](./LICENSE).
