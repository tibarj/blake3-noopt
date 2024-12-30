# Blake3

Blake3 php 8.3 implementation, for instructional purposes only

Reference: https://github.com/BLAKE3-team/BLAKE3-specs/blob/master/blake3.pdf

## Usage

```php
use Tibarj\Blake3Noopt\Blake3Hash;

$blake3 = new Blake3Hash(); //        hash mode
           // Blake3Hash($key); keyed_hash mode

$h = fopen('input.txt', 'r');
while (!feof($h)) {
    $blake3->absorb(fread($h, 500));
}
fclose($h);

// hash
$hash = $blake3->squeeze();

// extendable output
$h = fopen('output.txt', 'w');
while(..) {
    fwrite($h, $blake3->squeeze(500));
}
fclose($h);
```

Use environment variable `BLAKE3_NOOPT_DEBUG` to activate traces.
