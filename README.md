# Blake3

Blake3 php 8.3 implementation, for instructional purposes only

Reference: https://github.com/BLAKE3-team/BLAKE3-specs/blob/master/blake3.pdf

## Basic Usage

```php
use Tibarj\Blake3Noopt\Blake3Hash;

$hash = (new Blake3Hash())
    ->absorb($input)
    ->squeeze();
```

## Advanced Usage

### Hash / Keyed Hash Modes

```php
use Tibarj\Blake3Noopt\Blake3Hash;

$blake3 = new Blake3Hash();     //       hash mode
$blake3 = new Blake3Hash($key); // keyed_hash mode

$h = fopen('hash_input', 'r');
while (!feof($h)) {
    $blake3->absorb(fread($h, 500));
}
fclose($h);

// squeeze hash
$hash = $blake3->squeeze();

// extendable output
$h = fopen('hash_output', 'w');
while(..) {
    fwrite($h, $blake3->squeeze(500));
}
fclose($h);
```

### Derive Key Mode

```php
use Tibarj\Blake3Noopt\Blake3KD;

$blake3 = new Blake3KD();

// absorb key context
$h = fopen('key_context', 'r');
while (!feof($h)) {
    $blake3->absorb(fread($h, 500));
}
fclose($h);

// switch to key material absorption
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
$h = fopen('key_output', 'w');
while(..) {
    fwrite($h, $blake3->squeeze(500));
}
fclose($h);
```

## Traces

Use environment variable `BLAKE3_NOOPT_DEBUG` to activate traces.

```php
$hash = (new Blake3Hash())
    ->absorb('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.')
    ->squeeze();

echo bin2hex($hash); // 71fe44583a6268b56139599c293aeb854e5c5a9908eca00105d81ad5e22b7bb6
```

Generated trace:

```txt
Tibarj\Blake3Noopt\Blake3Hash::absorb 445 bytes
445 bytes remaining
Tibarj\Blake3Noopt\NodeCargo::__construct with capacity of 1024 and t=0
Loading cargo with 445 bytes
Tibarj\Blake3Noopt\NodeCargo::ingest 445 bytes at cargo offset 0
payload:
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

Tibarj\Blake3Noopt\Blake3Hash::squeeze
Tibarj\Blake3Noopt\Blake3Hash::shipCargo
Tibarj\Blake3Noopt\BinaryNode::__construct Node #0 of weight 1 as leaf
Tibarj\Blake3Noopt\Blake3Hash::processTree
Tibarj\Blake3Noopt\BinaryNode::traverse from Node #0 of weight 1
Yield Node #0 of weight 1
Tibarj\Blake3Noopt\Blake3Hash::processNode Node #0 of weight 1

Compress Node #0 of weight 1, Block #0

t0: 0
t1: 0
b: 64
flags: CHUNK_START

m:
4C6F7265 6D206970 73756D20 646F6C6F
72207369 7420616D 65742C20 636F6E73
65637465 74757220 61646970 69736369
6E672065 6C69742C 20736564 20646F20

v:
6A09E667 BB67AE85 3C6EF372 A54FF53A
510E527F 9B05688C 1F83D9AB 5BE0CD19
6A09E667 BB67AE85 3C6EF372 A54FF53A
00000000 00000000 00000040 00000001

Tibarj\Blake3Noopt\AbstractBlake3::unpack

Compress Node #0 of weight 1, Block #1

t0: 0
t1: 0
b: 64
flags:

m:
65697573 6D6F6420 74656D70 6F722069
6E636964 6964756E 74207574 206C6162
6F726520 65742064 6F6C6F72 65206D61
676E6120 616C6971 75612E20 55742065

v:
ED3E6F84 FA8DE6A0 05EDF2E8 089C9A94
A9AD0906 3E92FBE2 CF5A5C1E 83E45EEB
6A09E667 BB67AE85 3C6EF372 A54FF53A
00000000 00000000 00000040 00000000

Tibarj\Blake3Noopt\AbstractBlake3::unpack

Compress Node #0 of weight 1, Block #2

t0: 0
t1: 0
b: 64
flags:

m:
6E696D20 6164206D 696E696D 2076656E
69616D2C 20717569 73206E6F 73747275
64206578 65726369 74617469 6F6E2075
6C6C616D 636F206C 61626F72 6973206E

v:
5DC06F93 9861FFE1 7D3AE29C B3350400
46BB352D CD9159C6 BC628A87 92E5A05A
6A09E667 BB67AE85 3C6EF372 A54FF53A
00000000 00000000 00000040 00000000

Tibarj\Blake3Noopt\AbstractBlake3::unpack

Compress Node #0 of weight 1, Block #3

t0: 0
t1: 0
b: 64
flags:

m:
69736920 75742061 6C697175 69702065
78206561 20636F6D 6D6F646F 20636F6E
73657175 61742E20 44756973 20617574
65206972 75726520 646F6C6F 7220696E

v:
2B1F2AF6 7181525D 30BEF2B5 3AC8C257
F814446C 17F3992F 2A84DBFA 33499376
6A09E667 BB67AE85 3C6EF372 A54FF53A
00000000 00000000 00000040 00000000

Tibarj\Blake3Noopt\AbstractBlake3::unpack

Compress Node #0 of weight 1, Block #4

t0: 0
t1: 0
b: 64
flags:

m:
20726570 72656865 6E646572 69742069
6E20766F 6C757074 61746520 76656C69
74206573 73652063 696C6C75 6D20646F
6C6F7265 20657520 66756769 6174206E

v:
72351432 F0EFC435 BA64B546 A9EED213
4DC2B517 DEE37AE5 3F0A101A CA437B25
6A09E667 BB67AE85 3C6EF372 A54FF53A
00000000 00000000 00000040 00000000

Tibarj\Blake3Noopt\AbstractBlake3::unpack

Compress Node #0 of weight 1, Block #5

t0: 0
t1: 0
b: 64
flags:

m:
756C6C61 20706172 69617475 722E2045
78636570 74657572 2073696E 74206F63
63616563 61742063 75706964 61746174
206E6F6E 2070726F 6964656E 742C2073

v:
CF778D2A EE7915B3 F5E13CE3 EB8E0137
C32822E3 C43D9120 8EF755AD 385ED4C3
6A09E667 BB67AE85 3C6EF372 A54FF53A
00000000 00000000 00000040 00000000

Tibarj\Blake3Noopt\AbstractBlake3::unpack
Pad message with 3 bytes

Compress Node #0 of weight 1, Block #6

t0: 0
t1: 0
b: 61
flags: CHUNK_END ROOT

m:
756E7420 696E2063 756C7061 20717569
206F6666 69636961 20646573 6572756E
74206D6F 6C6C6974 20616E69 6D206964
20657374 206C6162 6F72756D 2E000000

v:
91EB7886 180310E0 5A164DC8 0755C285
64FFFC05 2A94DE5D AF0D40FD 0A0997BF
6A09E667 BB67AE85 3C6EF372 A54FF53A
00000000 00000000 0000003D 0000000A

Tibarj\Blake3Noopt\AbstractBlake3::unpack
Return output of Node #0 of weight 1
Tibarj\Blake3Noopt\AbstractBlake3::pack
```
