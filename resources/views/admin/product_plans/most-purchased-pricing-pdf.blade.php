<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Most-Purchased Product Plan Pricing</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 28px; color: #1f2937; background: #f3f4f6; }
        .card { max-width: 1200px; margin: auto; padding: 24px; border-radius: 12px; background: white; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        h1 { margin: 0; font-size: 24px; }
        p { color: #6b7280; }
        button { border: 0; border-radius: 8px; padding: 11px 18px; color: white; background: #2563eb; cursor: pointer; font-weight: 600; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; font-size: 12px; }
        th, td { padding: 9px; border: 1px solid #e5e7eb; text-align: left; }
        th { background: #111827; color: white; }
        td.number { text-align: right; white-space: nowrap; }
        .status { margin-left: 10px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
<main class="card">
    <h1>Most-Purchased Product Plan Pricing</h1>
    <p>Successful purchases ranked all-time · {{ $rows->count() }} unique plans · Generated {{ $generatedAt->format('d M Y H:i') }}</p>
    <button type="button" id="download-pdf">Download PDF</button><span id="status" class="status">Preparing download…</span>

    <table>
        <thead><tr><th>Popularity</th><th>Plan</th><th>Product / Network</th><th>L1</th><th>L2</th><th>L3</th><th>L4</th></tr></thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row['rank'] }}</td><td>{{ $row['plan_name'] }}</td><td>{{ $row['product'] }} / {{ $row['network'] }}</td>
                @for($level = 1; $level <= 4; $level++)<td class="number">NGN {{ number_format($row['level_'.$level], 2) }}</td>@endfor
            </tr>
        @empty
            <tr><td colspan="7">No successfully purchased product plans were found.</td></tr>
        @endforelse
        </tbody>
    </table>
</main>

<script src="{{ asset(env('APP_ASSETS_BASE_URL').'libs/jspdf/jspdf.umd.min.js') }}"></script>
<script src="{{ asset(env('APP_ASSETS_BASE_URL').'libs/jspdf-autotable/jspdf.plugin.autotable.min.js') }}"></script>
<script>
    const pricingRows = @json($rows);
    const generatedAt = @json($generatedAt->format('d M Y H:i'));

    function downloadPricingPdf() {
        const { jsPDF } = window.jspdf;
        const documentPdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
        documentPdf.setFontSize(16);
        documentPdf.text('Most-Purchased Product Plan Pricing', 14, 15);
        documentPdf.setFontSize(9);
        documentPdf.setTextColor(90);
        documentPdf.text(`Successful purchases ranked all-time | Generated ${generatedAt}`, 14, 21);
        documentPdf.autoTable({
            startY: 26,
            head: [['Popularity', 'Plan / identifiers', 'Product', 'Network', 'Size', 'Validity', 'Level 1', 'Level 2', 'Level 3', 'Level 4']],
            body: pricingRows.map(row => [
                row.rank,
                `${row.plan_name}\nID: ${row.product_plan_id}\nAutomation plan: ${row.automation_plan_id || '-'}`,
                row.product,
                row.network,
                row.size_mb ? `${row.size_mb} MB` : '-',
                row.validity_days ? `${row.validity_days} days` : '-',
                `NGN ${Number(row.level_1).toFixed(2)}`,
                `NGN ${Number(row.level_2).toFixed(2)}`,
                `NGN ${Number(row.level_3).toFixed(2)}`,
                `NGN ${Number(row.level_4).toFixed(2)}`,
            ]),
            styles: { fontSize: 6.5, cellPadding: 1.6, overflow: 'linebreak' },
            headStyles: { fillColor: [17, 24, 39] },
            columnStyles: { 1: { cellWidth: 65 }, 6: { halign: 'right' }, 7: { halign: 'right' }, 8: { halign: 'right' }, 9: { halign: 'right' } },
            didDrawPage: data => {
                documentPdf.setFontSize(7);
                documentPdf.setTextColor(110);
                documentPdf.text(`Page ${documentPdf.internal.getNumberOfPages()}`, 280, 202, { align: 'right' });
            }
        });
        documentPdf.save(`most-purchased-product-plan-pricing-${new Date().toISOString().slice(0, 10)}.pdf`);
        document.getElementById('status').textContent = 'PDF ready.';
    }

    document.getElementById('download-pdf').addEventListener('click', downloadPricingPdf);
    window.addEventListener('load', () => setTimeout(downloadPricingPdf, 300));
</script>
</body>
</html>
