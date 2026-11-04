<?php

use App\Models\WhatsappConfig;
use App\Services\Whatsapp\MegaWhatsappService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    WhatsappConfig::create([
        'token' => 'test-meta-token',
        'phone_number_id' => '123456789',
    ]);

    Http::fake([
        'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'message-id']]]),
    ]);
});

it('sends a Meta WhatsApp text payload with authentication', function () {
    $response = app(MegaWhatsappService::class)->sendText('2348012345678', 'Hello');

    expect($response['messages'][0]['id'])->toBe('message-id');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://graph.facebook.com/v23.0/123456789/messages'
            && $request->hasHeader('Authorization', 'Bearer test-meta-token')
            && $request['messaging_product'] === 'whatsapp'
            && $request['to'] === '2348012345678'
            && $request['type'] === 'text'
            && $request['text']['body'] === 'Hello';
    });
});

it('formats reply buttons for Meta WhatsApp', function () {
    app(MegaWhatsappService::class)->sendButtons('2348012345678', 'Choose', [
        ['id' => 'main_menu', 'title' => 'Main menu'],
        ['id' => 'help', 'title' => 'Help'],
    ]);

    Http::assertSent(fn (Request $request) => $request['type'] === 'interactive'
        && $request['interactive']['type'] === 'button'
        && $request['interactive']['action']['buttons'][0]['reply'] === [
            'id' => 'main_menu',
            'title' => 'Main menu',
        ]
    );
});

it('formats selectable list rows for Meta WhatsApp', function () {
    app(MegaWhatsappService::class)->sendList('2348012345678', 'Recent transactions', [
        ['id' => 15, 'title' => '1GB Data', 'description' => 'Successful'],
    ], 'View transactions');

    Http::assertSent(fn (Request $request) => $request['interactive']['type'] === 'list'
        && $request['interactive']['action']['button'] === 'View transactions'
        && $request['interactive']['action']['sections'][0]['rows'][0] === [
            'id' => '15',
            'title' => '1GB Data',
            'description' => 'Successful',
        ]
    );
});

it('formats an approved Meta utility template payload', function () {
    app(MegaWhatsappService::class)->sendTemplate(
        '2348012345678',
        'oresamsub_transaction_failed',
        ['Samuel', '1GB MTN', '490.00', '0803***5678', 'TXN-123', 'Provider unavailable'],
    );

    Http::assertSent(fn (Request $request) => $request['type'] === 'template'
        && $request['template']['name'] === 'oresamsub_transaction_failed'
        && $request['template']['language']['code'] === 'en'
        && $request['template']['components'][0]['parameters'][0] === [
            'type' => 'text',
            'text' => 'Samuel',
        ]
        && $request['template']['components'][0]['parameters'][5]['text'] === 'Provider unavailable'
    );
});
