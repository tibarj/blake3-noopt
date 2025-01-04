# Blake3

Blake3 php 8.3 implementation, for instructional purposes only

Reference: https://github.com/BLAKE3-team/BLAKE3-specs/blob/master/blake3.pdf

## Usage

### Hash mode

```php
use Tibarj\Blake3Noopt\Blake3Hash;

$blake3 = new Blake3Hash();

$h = fopen('input', 'r');
while (!feof($h)) {
    $blake3->absorb(fread($h, 500));
}
fclose($h);

// squeeze hash
$hash = $blake3->squeeze();

// extendable output
$h = fopen('output', 'w');
while(..) {
    fwrite($h, $blake3->squeeze(500));
}
fclose($h);
```

### Keyed Hash mode

```php
use Tibarj\Blake3Noopt\Blake3Hash;
use Tibarj\Blake3Noopt\Strategy;

$blake3 = new Blake3Hash($key, Strategy::KEYED_HASH);

$h = fopen('input', 'r');
while (!feof($h)) {
    $blake3->absorb(fread($h, 500));
}
fclose($h);

// hash
$hash = $blake3->squeeze();

// extendable output
$h = fopen('output', 'w');
while(..) {
    fwrite($h, $blake3->squeeze(500));
}
fclose($h);
```

### Derive Key mode

```php
use Tibarj\Blake3Noopt\Blake3KD;

$blake3 = new Blake3KD();

// absorb key context
$h = fopen('key_context', 'r');
while (!feof($h)) {
    $blake3->absorb(fread($h, 500));
}
fclose($h);

// switch to key material
$blake3->ratchet();

// absorb key material
$h = fopen('key_material', 'r');
while (!feof($h)) {
    $blake3->absorb(fread($h, 500));
}
fclose($h);

// hash
$hash = $blake3->squeeze();

// extendable output
$h = fopen('output', 'w');
while(..) {
    fwrite($h, $blake3->squeeze(500));
}
fclose($h);
```

Use environment variable `BLAKE3_NOOPT_DEBUG` to activate traces.
