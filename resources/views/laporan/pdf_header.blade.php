<style>
    .pdf-header {
        display: flex;
        align-items: center;
        gap: 15px;
        text-align: left;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #333;
    }
    .pdf-header img {
        width: 60px;
        height: 60px;
        object-fit: contain;
    }
    .pdf-header-text h1 {
        margin: 0;
        font-size: 20px;
        font-weight: bold;
        color: #1e3a8a;
    }
    .pdf-header-text p {
        margin: 3px 0;
        font-size: 11px;
        color: #555;
    }
</style>

<div class="pdf-header">
    <div>
        <img src="{{ public_path('foto/logobimbel.png') }}" alt="Logo Bimbel Alwi" />
    </div>
    <div class="pdf-header-text">
        <h1>BIMBEL ALWI COLLEGE</h1>
        <p>Jalan Kebun Manggis Gang Salam 619 CD RT 04</p>
        <p>Kelurahan Kepandean Baru, Kecamatan Ilir Timur</p>
        <p>📞 0899-4432-225</p>
    </div>
</div>
