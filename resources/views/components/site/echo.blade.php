@php
    $broadcaster = config('broadcasting.default');

    $options = [
        'broadcaster' => $broadcaster,
        'key' => config("broadcasting.connections.{$broadcaster}.key"),
        'wsHost' => config("broadcasting.connections.{$broadcaster}.options.host"),
        'wsPort' => config("broadcasting.connections.{$broadcaster}.options.port", 80),
        'wssPort' => config("broadcasting.connections.{$broadcaster}.options.port", 443),
        'forceTLS' => config("broadcasting.connections.{$broadcaster}.options.useTLS", false),
        'enabledTransports' => ['ws', 'wss'],
    ];
@endphp

<script>
    window.EchoConfig = @json($options);
</script>
