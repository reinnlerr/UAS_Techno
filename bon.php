<?php
include 'db.php';
if(!isset($_GET['pesanan_id'])){
    header("Location: index.php");
    exit;
}
$pesanan_id = intval($_GET['pesanan_id']);
$query = $conn->query("SELECT p.*, l.nama_lapangan, l.lokasi, l.harga_per_jam 
                        FROM pesanan p 
                        JOIN lapangan l ON p.lapangan_id = l.id 
                        WHERE p.id = $pesanan_id");
if($query->num_rows == 0){
    echo "<script>alert('Data pesanan tidak ditemukan.'); window.location='index.php';</script>";
    exit;
}
$data = $query->fetch_assoc();
$no_bon = 'FB-' . str_pad($data['id'], 5, '0', STR_PAD_LEFT);
$tanggal_cetak = date('d/m/Y H:i:s');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bon Pembayaran - FasilBook</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
            padding: 40px 15px;
        }

        .bon-container {
            max-width: 480px;
            width: 100%;
        }

        .bon-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .bon-header {
            background: linear-gradient(135deg, #2FB95D 0%, #1a8a3e 100%);
            color: white;
            padding: 30px 25px;
            text-align: center;
            position: relative;
        }

        .bon-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: radial-gradient(circle, transparent 6px, #ffffff 6px);
            background-size: 20px 20px;
            background-position: 10px 0;
        }

        .bon-header .check-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 28px;
            animation: popIn 0.5s ease-out;
        }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .bon-header h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            margin: 0;
            font-size: 1.3rem;
        }

        .bon-header .bon-number {
            font-family: 'Roboto Mono', monospace;
            font-size: 0.85rem;
            opacity: 0.9;
            margin-top: 4px;
        }

        .bon-body {
            padding: 30px 25px 20px;
        }

        .bon-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e0e0e0;
            font-size: 0.92rem;
        }

        .bon-row:last-child {
            border-bottom: none;
        }

        .bon-row .label {
            color: #888;
            font-weight: 400;
        }

        .bon-row .value {
            color: #333;
            font-weight: 500;
            text-align: right;
            max-width: 60%;
        }

        .bon-total {
            background: #f8f9fa;
            margin: 0 25px;
            padding: 15px 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px dashed #2FB95D;
        }

        .bon-total .label {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        .bon-total .amount {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: #2FB95D;
            font-size: 1.4rem;
        }

        .bon-footer {
            padding: 20px 25px 30px;
            text-align: center;
        }

        .bon-footer .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #d4edda;
            color: #155724;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .bon-footer .timestamp {
            font-size: 0.78rem;
            color: #aaa;
            font-family: 'Roboto Mono', monospace;
        }

        .btn-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-actions .btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-print {
            background: #2FB95D;
            color: white;
            border: none;
        }

        .btn-print:hover {
            background: #24934a;
            color: white;
        }

        .btn-home {
            background: #f0f0f0;
            color: #333;
            border: none;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-home:hover {
            background: #e0e0e0;
            color: #333;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .bon-card {
                box-shadow: none !important;
                border: 1px solid #ddd;
            }
            .btn-actions {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="bon-container">
        <div class="bon-card">
            <div class="bon-header">
                <div class="check-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h4>Pembayaran Berhasil!</h4>
                <div class="bon-number"><?php echo $no_bon; ?></div>
            </div>

            <div class="bon-body">
                <div class="bon-row">
                    <span class="label"><i class="fa fa-futbol me-1"></i> Lapangan</span>
                    <span class="value"><?php echo htmlspecialchars($data['nama_lapangan']); ?></span>
                </div>
                <div class="bon-row">
                    <span class="label"><i class="fa fa-map-marker-alt me-1"></i> Lokasi</span>
                    <span class="value"><?php echo htmlspecialchars($data['lokasi']); ?></span>
                </div>
                <div class="bon-row">
                    <span class="label"><i class="fa fa-user me-1"></i> Pemesan</span>
                    <span class="value"><?php echo htmlspecialchars($data['nama_pemesan']); ?></span>
                </div>
                <div class="bon-row">
                    <span class="label"><i class="fa fa-calendar me-1"></i> Tanggal Main</span>
                    <span class="value"><?php echo date('d M Y', strtotime($data['tanggal_booking'])); ?></span>
                </div>
                <div class="bon-row">
                    <span class="label"><i class="fa fa-clock me-1"></i> Jam Mulai</span>
                    <span class="value"><?php echo substr($data['jam_mulai'], 0, 5) . ' WIB'; ?></span>
                </div>
                <div class="bon-row">
                    <span class="label"><i class="fa fa-credit-card me-1"></i> Metode Bayar</span>
                    <span class="value">QRIS</span>
                </div>
            </div>

            <div class="bon-total">
                <span class="label">Total Bayar</span>
                <span class="amount">Rp <?php echo number_format($data['harga_per_jam'], 0, ',', '.'); ?></span>
            </div>

            <div class="bon-footer">
                <div class="status-badge">
                    <i class="fas fa-check-circle"></i> LUNAS
                </div>
                <div class="timestamp">Dicetak: <?php echo $tanggal_cetak; ?></div>

                <div class="btn-actions">
                    <button onclick="window.print()" class="btn btn-print">
                        <i class="fa fa-print me-1"></i> Cetak Bon
                    </button>
                    <a href="index.php" class="btn btn-home">
                        <i class="fa fa-home me-1"></i> Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
