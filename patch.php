<?php
$file = 'app/Http/Controllers/ResultController.php';
$content = file_get_contents($file);
$content = str_replace(
    "fn (\$exam) => \$exam->where('guru_id', \$guruId)",
    "fn (\$exam) => \$exam->where(fn (\$q) => \$q->where('guru_id', \$guruId)->orWhere('guru_pengawas_id', \$guruId))",
    $content
);
$content = str_replace(
    "abort_unless(\$attempt->exam->guru_id === \$request->user()->guru?->id, 403, 'Anda tidak memiliki akses ke hasil ujian ini.');",
    "abort_unless(\$attempt->exam->guru_id === \$request->user()->guru?->id || \$attempt->exam->guru_pengawas_id === \$request->user()->guru?->id, 403, 'Anda tidak memiliki akses ke hasil ujian ini.');",
    $content
);
$content = str_replace(
    "fn (\$q) => \$q->where('guru_id', \$request->user()->guru?->id)",
    "fn (\$q) => \$q->where(fn (\$sq) => \$sq->where('guru_id', \$request->user()->guru?->id)->orWhere('guru_pengawas_id', \$request->user()->guru?->id))",
    $content
);
$content = str_replace(
    "fn (\$exam) => \$exam->where('guru_id', \$request->user()->guru?->id)",
    "fn (\$exam) => \$exam->where(fn (\$q) => \$q->where('guru_id', \$request->user()->guru?->id)->orWhere('guru_pengawas_id', \$request->user()->guru?->id))",
    $content
);
file_put_contents($file, $content);
