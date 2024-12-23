<?php

use nostriphant\NIP01\Key;
use function \Pest\vectors_secp256k1;

it('generates a public key without an argument', function() {
    $private_key = Key::fromHex('435790f13406085d153b10bd9e00a9f977e637f10ce37db5ccfc5d3440c12d6c');
    expect($private_key(Key::public()))->toBe('89ac55aeeb301252da33b51ca4d189cb1d665b8f00618f5ea72c2ec59ca555e9');
});

it('converts between bytes and hexidecimal', function () {

    $public_key_hex = '7e7e9c42a91bfef19fa929e5fda1b72e0ebc1a4c1141673e2794234d86addf4e';
    //$public_key_bech32 = 'npub10elfcs4fr0l0r8af98jlmgdh9c8tcxjvz9qkw038js35mp4dma8qzvjptg';
    $private_key_hex = '67dea2ed018072d675f5415ecfaed7d2597555e202d85b3d65ea4e58d2d92ffa';
    //$private_key_bech32 = 'nsec1vl029mgpspedva04g90vltkh6fvh240zqtv9k0t9af8935ke9laqsnlfe5';

    $key = Key::fromHex($private_key_hex);
    expect($key(fn() => func_get_arg(0)))->toBe($private_key_hex);
    expect($key(Key::public()))->toBe($public_key_hex);
});


it('converts between bytes and hexidecimal for provided functions', function () {
    $private_key = \Pest\key_sender();
    expect($private_key(Key::public()))->toBe(Pest\pubkey_sender());

    $private_key = \Pest\key_recipient();
    expect($private_key(Key::public()))->toBe(Pest\pubkey_recipient());
})->with();

it('works with paulmillrs vectors', function ($vector) {
    // https://github.com/paulmillr/noble-secp256k1/blob/main/test/wycheproof/ecdh_secp256k1_test.json
    $secret = Key::fromHex($vector->private)(Key::sharedSecret(substr($vector->public, 46)));
    expect(str_pad($secret, 64, '0', STR_PAD_LEFT))->toBe($vector->shared);
})->with(array_filter(vectors_secp256k1()->testGroups[0]->tests, fn($vector) => $vector->result === 'valid'));

it('can sign a string and verify a signature', function () {
    $private_key = Key::fromHex('435790f13406085d153b10bd9e00a9f977e637f10ce37db5ccfc5d3440c12d6c');

    expect($private_key(Key::public()))->toBe('89ac55aeeb301252da33b51ca4d189cb1d665b8f00618f5ea72c2ec59ca555e9');

    $hash = hash('sha256', 'hallo world');
    $signature = $private_key(Key::signer($hash));

    expect(Key::verify('89ac55aeeb301252da33b51ca4d189cb1d665b8f00618f5ea72c2ec59ca555e9', $signature, $hash))->toBeTrue();
});
