<?php

// phpunit --testdox tests

declare(strict_types=1);

namespace Tibarj\Blake3Noopt\tests;

use PHPUnit\Framework\TestCase;
use Tibarj\Blake3Noopt\Blake3Hash;

final class Blake3HashTest extends TestCase
{
    function data(): array
    {
        return [
            [
                [''],
                [
                    // 32 bytes
                    'af1349b9f5f9a1a6a0404dea36dcc9499bcb25c9adc112b7cc9a93cae41f3262',
                ],
                null, // no key
            ],
            [
                [''],
                [
                    // 64 bytes
                    'af1349b9f5f9a1a6a0404dea36dcc9499bcb25c9adc112b7cc9a93cae41f3262e00f03e7b69af26b7faaf09fcd333050338ddfe085b8cc869ca98b206c08243a',
                ],
                null, // no key
            ],
            [
                [''],
                [ // 256 bytes in a go
                    'af1349b9f5f9a1a6a0404dea36dcc9499bcb25c9adc112b7cc9a93cae41f3262e00f03e7b69af26b7faaf09fcd333050338ddfe085b8cc869ca98b206c08243a26f5487789e8f660afe6c99ef9e0c52b92e7393024a80459cf91f476f9ffdbda7001c22e159b402631f277ca96f2defdf1078282314e763699a31c5363165421'
                ],
                null, // no key
            ],
            [
                [''],
                [ // 1200 bytes in a go
                    'af1349b9f5f9a1a6a0404dea36dcc9499bcb25c9adc112b7cc9a93cae41f3262e00f03e7b69af26b7faaf09fcd333050338d'
                  . 'dfe085b8cc869ca98b206c08243a26f5487789e8f660afe6c99ef9e0c52b92e7393024a80459cf91f476f9ffdbda7001c22e'
                  . '159b402631f277ca96f2defdf1078282314e763699a31c5363165421cce14d30f8a03e49ee25d2ea3cd48a568957b378a65a'
                  . 'f65fc35fb3e9e12b81ca2d82cdee16c68908a6772f827564336933c89e6908b2f9c7d1811c0eb795cbd5898fe6f5e8af7633'
                  . '19ca863718a59aff3d99660ef642483e217ef0c8785827284fea90d42225e3cdd6a179bee852fd24e7d45b38c27b9c2f9469'
                  . 'ea8dbdb893f00e28534c7d15b59badd5a5bdeb090e98eb93c5b2f42101394acb7c72e9b60094d5442096754600db8c0fa6db'
                  . 'dfea154c324c07bf17b7ab0d1488ae5ef76cb7611baef17087d84c08b4f950d3d85e00e7001813fe029a10722bb003531d5a'
                  . 'e406386e78cca4ca7cace8a41d294f6ee3b1c645832109b5b19304360b8ab79581e351c518849eaa7c7e14f37ba5b769d2ca'
                  . 'f191f9ddee2d4982b6213947a7d047a03f5e456f2588f56e4075c756a319299fba4001c4b6fb89fbfd93b0739dc684424a43'
                  . '9cefb447d5e191919c4581bc153bd2f2fae39758f1322ae52ea8b2d859887a71f70c03e28765709711950c2c06bf5c7d1bb6'
                  . 'c235f722ce6db047fe97cf74b87adbd6531cb14a1193a8974f939dd2eb21335793880279905402dbda8b5ec0a7c82a69151b'
                  . 'b42f7126e4157a510c6123139815ba3df3fd1d810795d1f4f49cb8b0d63d8d07833ce95fcff2b8b8677d1f6c3ee3cf2a00ce'
                  . '72a32e93f5e225a065a0726dc5c9ad5c26f2c3560e401ba5079c3d63a8b29175bc9597b09a2be664e6641f2d2ebfafe58d5c'
                  . '025ee367396b4c0e31f9d761b779ff27dbab678cfbb3c62460cc68a4c3187e9788e045ec924371c3027903a42059d1ed6594'
                  . '06706c5e4381c931886a034e20689ffa78221e39b42326a9725c5d669d5e2abaa1c4640afc7e4d3a5ff5c5513f1b13bf865f'
                  . '4f02ec09453dbd0bcd1d0ac3444141cc78b662f00811f095d1a1614edcb516c70fb3bbf4c9ed58f8fbbdde8cb1b5497585c5'
                  . '3fb33eb7a98810780056c9952848f129d5a87dd36774c1b91e135c1acef799e6e4320fb862c3619f6874ce0d7550d260308d'
                  . '7e309eeea5026a534d37dfa4f703bf185c015d99d88a1e350639634d1c7f1de79faebc0dfecac66089e6f44c916debc12965'
                  . 'dd0ecfddf8ad4cafb5abc45fc9fca9780c26f457ea9ddcf5370a4d042bc5b9bfa87fac10f88b170cd22cb9ab2255b2515292'
                  . '72baddf757ad471c4935363495b8e626421859ff304f6d5d527aae2af7444f3e14c8cd41f9bb1e19a1418e08a5b535c79554'
                  . '7746a7059c6c5a8d1e0c581d29850767087c8688c8011bbd6a68f4b3ebb9cfeea26d086058b93c7c3ee9f1a6acd5283095cc'
                  . 'ae0583c7d7b99cda4750deffeb2a5398e2f2f0ca88781f8ea30e61cd960e8b115a616783fe4269f794a41ff3f97e8573ee7b'
                  . 'e025d3287707acbf7fecf69d888ff9718349e07c0a011ad14dd9ce45b509579e5cee22d1943cba33b5fa8ee0d9ddead78e31'
                  . 'efb81345ba1073b98dd9a7e541d98f49be01e63bb15fc6d6a2c75331df5c34ec098401d246e0fe5647645dadfcebf8363766'
                ],
                null, // no key
            ],
            [
                [''],
                [ // 1200 bytes in packets of 50 bytes
                    'af1349b9f5f9a1a6a0404dea36dcc9499bcb25c9adc112b7cc9a93cae41f3262e00f03e7b69af26b7faaf09fcd333050338d',
                    'dfe085b8cc869ca98b206c08243a26f5487789e8f660afe6c99ef9e0c52b92e7393024a80459cf91f476f9ffdbda7001c22e',
                    '159b402631f277ca96f2defdf1078282314e763699a31c5363165421cce14d30f8a03e49ee25d2ea3cd48a568957b378a65a',
                    'f65fc35fb3e9e12b81ca2d82cdee16c68908a6772f827564336933c89e6908b2f9c7d1811c0eb795cbd5898fe6f5e8af7633',
                    '19ca863718a59aff3d99660ef642483e217ef0c8785827284fea90d42225e3cdd6a179bee852fd24e7d45b38c27b9c2f9469',
                    'ea8dbdb893f00e28534c7d15b59badd5a5bdeb090e98eb93c5b2f42101394acb7c72e9b60094d5442096754600db8c0fa6db',
                    'dfea154c324c07bf17b7ab0d1488ae5ef76cb7611baef17087d84c08b4f950d3d85e00e7001813fe029a10722bb003531d5a',
                    'e406386e78cca4ca7cace8a41d294f6ee3b1c645832109b5b19304360b8ab79581e351c518849eaa7c7e14f37ba5b769d2ca',
                    'f191f9ddee2d4982b6213947a7d047a03f5e456f2588f56e4075c756a319299fba4001c4b6fb89fbfd93b0739dc684424a43',
                    '9cefb447d5e191919c4581bc153bd2f2fae39758f1322ae52ea8b2d859887a71f70c03e28765709711950c2c06bf5c7d1bb6',
                    'c235f722ce6db047fe97cf74b87adbd6531cb14a1193a8974f939dd2eb21335793880279905402dbda8b5ec0a7c82a69151b',
                    'b42f7126e4157a510c6123139815ba3df3fd1d810795d1f4f49cb8b0d63d8d07833ce95fcff2b8b8677d1f6c3ee3cf2a00ce',
                    '72a32e93f5e225a065a0726dc5c9ad5c26f2c3560e401ba5079c3d63a8b29175bc9597b09a2be664e6641f2d2ebfafe58d5c',
                    '025ee367396b4c0e31f9d761b779ff27dbab678cfbb3c62460cc68a4c3187e9788e045ec924371c3027903a42059d1ed6594',
                    '06706c5e4381c931886a034e20689ffa78221e39b42326a9725c5d669d5e2abaa1c4640afc7e4d3a5ff5c5513f1b13bf865f',
                    '4f02ec09453dbd0bcd1d0ac3444141cc78b662f00811f095d1a1614edcb516c70fb3bbf4c9ed58f8fbbdde8cb1b5497585c5',
                    '3fb33eb7a98810780056c9952848f129d5a87dd36774c1b91e135c1acef799e6e4320fb862c3619f6874ce0d7550d260308d',
                    '7e309eeea5026a534d37dfa4f703bf185c015d99d88a1e350639634d1c7f1de79faebc0dfecac66089e6f44c916debc12965',
                    'dd0ecfddf8ad4cafb5abc45fc9fca9780c26f457ea9ddcf5370a4d042bc5b9bfa87fac10f88b170cd22cb9ab2255b2515292',
                    '72baddf757ad471c4935363495b8e626421859ff304f6d5d527aae2af7444f3e14c8cd41f9bb1e19a1418e08a5b535c79554',
                    '7746a7059c6c5a8d1e0c581d29850767087c8688c8011bbd6a68f4b3ebb9cfeea26d086058b93c7c3ee9f1a6acd5283095cc',
                    'ae0583c7d7b99cda4750deffeb2a5398e2f2f0ca88781f8ea30e61cd960e8b115a616783fe4269f794a41ff3f97e8573ee7b',
                    'e025d3287707acbf7fecf69d888ff9718349e07c0a011ad14dd9ce45b509579e5cee22d1943cba33b5fa8ee0d9ddead78e31',
                    'efb81345ba1073b98dd9a7e541d98f49be01e63bb15fc6d6a2c75331df5c34ec098401d246e0fe5647645dadfcebf8363766',
                ],
                null, // no key
            ],
            [
                // 1 byte input
                ['a'],
                [
                    // 32 bytes
                    '17762fddd969a453925d65717ac3eea21320b66b54342fde15128d6caf21215f',
                ],
                null, // no key
            ],
            [
                // 32 bytes input
                ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'],
                [
                    // 32 bytes
                    'fc1edcfa1065bc3a170fa5cda85c1cd6a3b7e73b3215e7a705c96e387910878f',
                ],
                null, // no key
            ],
            [
                // 64 bytes input
                ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'],
                [
                    // 32 bytes
                    '1a2a060cf56e4a859d80723cac9e2391d3c09a33008483e5424c57fe68629b79',
                ],
                null, // no key
            ],
            [
                // 64 bytes input (1 block)
                ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'],
                [
                    // 32 bytes
                    '472c51290d607f100d2036fdcedd7590bba245e9adeb21364a063b7bb4ca81c7',
                ],
                null, // no key
            ],
            [
                // 65 bytes input (2 blocks)
                ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'],
                [
                    // 32 bytes
                    'f345679d9055e53939e92c04ff4f6c9d824b849810d4b598f54baa23336cde99',
                ],
                null, // no key
            ],
            [
                // 64 bytes input (1 block)
                ['a9BO2Hkqd3axehDsgpMUjw06rbrzpt98gjjmsNW15HeqHii2KaYzTvvxcl8LZ8pL'],
                [
                    // 32 bytes
                    '97e8c941dfaf40d5b9ac31e751efadfa884ffcc064589870ff64318e6ce5771f',
                ],
                null, // no key
            ],
            [
                [
                    // 1024 bytes input (1 chunk)
                    'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  . 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  . '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  . 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  . 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  . 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  . 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  . '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  . 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  . 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  . 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  . 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  . 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  . 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  . '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  . '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'
                ],
                [
                    // 32 bytes
                    'b0ba74e5c90cdcbe1838a162be1998a04b68f8a8ec5248a28017d21d9c8d66aa',
                ],
                null, // no key
            ],
            [
                [
                    // 1024+1 bytes input (2 chunks)
                    'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  . 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  . '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  . 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  . 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  . 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  . 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  . '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  . 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  . 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  . 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  . 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  . 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  . 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  . '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  . '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'
                  . 'a'
                ],
                [
                    // 32 bytes
                    '6de5ae5d78a8030338978c08b71b006c7d58cf01ceda89c674470cb632fe4388',
                ],
                null, // no key
            ],
            [
                [
                    // 1024+1 bytes input (2 chunks)
                    'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  , 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  , '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  , 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  , 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  , 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  , 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  , '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  , 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  , 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  , 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  , 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  , 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  , 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  , '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  , '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'
                  , 'a'
                ],
                [
                    // 32 bytes
                    '6de5ae5d78a8030338978c08b71b006c7d58cf01ceda89c674470cb632fe4388',
                ],
                null, // no key
            ],
            [
                [
                    // 2*1024+1 bytes input (3 chunks)
                    'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  . 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  . '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  . 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  . 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  . 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  . 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  . '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  . 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  . 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  . 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  . 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  . 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  . 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  . '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  . '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'

                  . 'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  . 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  . '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  . 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  . 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  . 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  . 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  . '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  . 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  . 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  . 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  . 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  . 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  . 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  . '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  . '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'
                  . 'a'
                ],
                [
                    // 32 bytes
                    'c46cd0cb11c0436f05ee62a9429297cd53218704d02555c3911db020b3b5256e',
                ],
                null, // no key
            ],
            [
                [
                    // 4*1024+1 bytes input (5 chunks)
                    'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  . 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  . '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  . 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  . 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  . 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  . 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  . '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  . 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  . 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  . 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  . 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  . 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  . 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  . '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  . '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'

                  . 'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  . 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  . '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  . 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  . 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  . 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  . 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  . '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  . 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  . 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  . 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  . 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  . 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  . 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  . '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  . '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'

                  . 'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  . 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  . '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  . 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  . 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  . 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  . 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  . '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  . 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  . 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  . 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  . 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  . 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  . 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  . '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  . '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'

                  . 'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  . 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  . '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  . 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  . 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  . 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  . 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  . '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  . 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  . 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  . 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  . 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  . 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  . 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  . '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  . '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'

                  . 'a'
                ],
                [
                    // 128 bytes
                    '7fce07f4413fb3d4e13adc6367112f6d31884ced9972696f64dd2218f626a0bb'
                  . 'f28633da9eda7218cbdcb47af0bc3922e58bf214376bc519442dfdcce5335089'
                  . 'bfe91db9706fafc618d3080b9d223dfedab45bce1ec9fe54a9e9f011754155cc'
                  . '49694157754f7e0e8d0213f85128267255bf4835e024bd5ce3d86e5b422d6505',
                ],
                null, // no key
            ],
            [
                [
                    // keyed 1 byte input (1 chunk)
                  ''
                ],
                [
                    // 32 bytes
                    '89f69568098053b97950f01d9856a3385f8032e9410f4c5190cfdea90635ef10',
                ],
                'IvFOguXgaMxYU35R82zsyRPhS1qMMOAH', // 32 bytes key
            ],
            [
                [
                    // keyed 1 byte input (1 chunk)
                  'a'
                ],
                [
                    // 32 bytes
                    'df2a867fd5e1b442f1342710a22e8cbf788bff7816907e76260f4ccf865aae4d',
                ],
                'IvFOguXgaMxYU35R82zsyRPhS1qMMOAH', // 32 bytes key
            ],
            [
                [
                    // keyed 1024+1 bytes input (2 chunks)
                    'FNFeXVoaeRz_l8pjqpOK3_nBbRTfvMYNg8nA-4_9MDjuvDitc4UVkTMi5mt1J2v2'
                  , 'uey3Hix6z8eKZgv4CZLVMs7WmHWdmmJpxmYiQkZG_PC1hg7Xl8cfUTdlvuBcQh6v'
                  , '64rU8khboZWo8taA5zt_dnb0q_jdxw1Qy1vnonrIIYOWzSQOnsjgaCdpuZ7am7_s'
                  , 'OOaZSQZVGbqDj-qgwNnS9NpMTn0EyHBonYJZwi3LDnYEdOGXDKCC8J--n5D6GFRP'
                  , 'jgDigsRmuTPdzH5cc39Fmfp7v98eidig1KdqHrGoMuX-m1orrs0-AJMv3qiFcD69'
                  , 'x3me-WXaIdnkwbblVsX0yFQMPdW4ss9R3QU8w3SWS8rimD0gnwoqQrQtJ_OmpgAv'
                  , 'yqKxFQU2Jqt5kznsXtuqueR1EcBlLKPCO8sp9qvmNYvms5qs9mfwoJpg0a9p3Lcz'
                  , '_Q220xDdNlDk_GJ9o1MOXFTpyFWvoVAXUrBxsqypWNCYcTO8XKawjEN8OwiKlevN'
                  , 'AZ9WbWyenPDzGjfHOB2j_7zFXoNtTKHc448_gUN0hYZxZ7wF3G7vg_4k-aqSpIGW'
                  , 'FhaihjWilNpyotzu7EOpfBODEyvRenLPdwIspWaZgIobQXPGgbAHwkE27CnebD1c'
                  , 'lLEQN-vXDacHtvDcSKOymi_h0Yqd84zAmsmMASJ027AEbxh7pJiDKIBlVWLnMS9F'
                  , 'ft9akN_UkUHD-6cZNSJfYAIMGY0yQZM26Ky4zoBPeac9UNoSVimdCQ5D4JEJW0aE'
                  , 'moZmR4d0BkfYi8RB8HLACEAmAKSI4r2JSrjjj52JMw29NKYQqcRUWskugBFG9D3T'
                  , 'ulGTOyjZbOKIOoSLzwSvFP_rkYM3pngrppNRpq5y6PmUs6VbhQv1RTbRnHL2fZ6y'
                  , '0sUZGxRJmuNplihT878zLX51HrY4gKU-jVXIp7GZuPPj5WHlVR26K7FHXKYfeCYy'
                  , '6zQhDSslbY4gmx5ezT7PVXoGe6ktOrQJKBQME6_hpf_P6V_iVsgyW8FuYzH98D6a'
                  , 'a'
                ],
                [
                    // 32 bytes
                    '7b4e98da5df78ac8445b098d6a3ebb6697ae2ad8b9ef084e57d67c35e8833c7f',
                ],
                'IvFOguXgaMxYU35R82zsyRPhS1qMMOAH', // 32 bytes key
            ],
        ];
    }

    /**
     * @dataProvider data
     */
    function test(array $input, array $expected, ?string $key)
    {
        $blake3 = new Blake3Hash($key);
        foreach ($input as $packet) {
            $blake3->absorb($packet);
        }

        foreach ($expected as $packet) {
            $hash = $blake3->squeeze(strlen($packet) / 2);
            $this->assertEquals($packet, bin2hex($hash));
        }
    }
}
