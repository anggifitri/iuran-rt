<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pembayarans = \App\Models\Pembayaran::all();
foreach ($pembayarans as $p) {
    if ($p->warga) {
        $p->update(['rt_number' => $p->warga->rt_number]);
    } else {
        $p->update(['rt_number' => '000']);
    }
}
echo "Done!\n";
