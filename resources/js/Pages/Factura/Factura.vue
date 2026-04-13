<template>
    <div class="receipt-container">
        <div class="receipt">
            <!-- ================= HEADER PREMIUM ================= -->
            <div class="header">
                <div class="header-banner">
                    <div class="banner-pattern"></div>
                </div>

                <div class="logo-section">
                    <div class="logo">
                        <div class="logo-squares">
                            <div class="square square-orange"></div>
                            <div class="square square-blue"></div>
                            <div class="square square-purple"></div>
                            <div class="square square-green"></div>
                            <div class="square square-cyan"></div>
                        </div>
                        <h1 class="company-name">we collab</h1>
                        <p class="company-tagline">
                            SOLUCIONES PARA TU ORGANIZACIÓN
                        </p>
                    </div>
                </div>

                <div class="title-section">
                    <div class="title-badge">DOCUMENTO OFICIAL</div>
                    <h2 class="receipt-title">RECIBO DE PAGO</h2>
                    <p class="receipt-subtitle">
                        Comprobante Fiscal Digital por Internet
                    </p>
                </div>
            </div>

            <!-- ================= BODY PREMIUM ================= -->
            <div class="body">
                <!-- Folio y Referencia rápida -->
                <div class="quick-info">
                    <div class="quick-item">
                        <span class="quick-label">No. de Transacción:</span>
                        <span class="quick-value">WEC-2026-0411-001</span>
                    </div>
                    <div class="quick-item">
                        <span class="quick-label">UUID:</span>
                        <span class="quick-value"
                            >a1b2c3d4-e5f6-7890-abcd-ef1234567890</span
                        >
                    </div>
                </div>

                <!-- Dos columnas principales -->
                <div class="two-columns">
                    <!-- Datos del Comprobante -->
                    <div class="info-card">
                        <div class="card-header">
                            <div class="card-icon">📄</div>
                            <h3 class="card-title">DATOS DEL COMPROBANTE</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-label">Folio fiscal</div>
                                <div class="info-value">we collab</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Fecha de emisión</div>
                                <div class="info-value">
                                    11 de abril de 2026
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">
                                    No. de autorización
                                </div>
                                <div class="info-value">11/04/2026</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Referencia</div>
                                <div class="info-value">11/04/2026</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Método de pago</div>
                                <div class="info-value">
                                    Pago en una sola exhibición
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Cliente -->
                    <div class="info-card">
                        <div class="card-header">
                            <div class="card-icon">👤</div>
                            <h3 class="card-title">DATOS DEL CLIENTE</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-label">Razón social</div>
                                <div class="info-value">we collab</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">RFC</div>
                                <div class="info-value">WEC210415xxx</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">
                                    Titular de la cuenta
                                </div>
                                <div class="info-value">Jose Gonzales Leon</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Plan contratado</div>
                                <div class="info-value">Jose Gonzales Leon</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Correo electrónico</div>
                                <div class="info-value">
                                    contacto@we-collab.tech
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle del Pago Mejorado -->
                <div class="detail-section">
                    <div class="section-header">
                        <div class="section-icon">💳</div>
                        <h3 class="section-title">DETALLE DEL PAGO</h3>
                        <div class="section-line"></div>
                    </div>

                    <div class="detail-table">
                        <table class="payment-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%">Concepto</th>
                                    <th style="width: 40%">Descripción</th>
                                    <th style="width: 15%">Cantidad</th>
                                    <th style="width: 20%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Servicio profesional</td>
                                    <td>we collab - Servicio mensual</td>
                                    <td>1</td>
                                    <td>$1,250.00</td>
                                </tr>
                                <tr class="empty-row">
                                    <td colspan="4" style="height: 80px">?</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="payment-methods">
                            <div class="method-row">
                                <span class="method-label"
                                    >Método de pago:</span
                                >
                                <span class="method-value">we collab</span>
                            </div>
                            <div class="method-row">
                                <span class="method-label">Banco emisor:</span>
                                <span class="method-value">we collab</span>
                            </div>
                            <div class="method-row">
                                <span class="method-label">Moneda:</span>
                                <span class="method-value"
                                    >we collab (MXN)</span
                                >
                            </div>
                            <div class="method-row">
                                <span class="method-label"
                                    >Tipo de cambio:</span
                                >
                                <span class="method-value">1.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totales -->
                <div class="totals-section">
                    <div class="totals-card">
                        <div class="totals-left">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>$1,250.00</span>
                            </div>
                            <div class="total-row">
                                <span>IVA (16%):</span>
                                <span>$200.00</span>
                            </div>
                            <div class="total-row total-highlight">
                                <span>Total:</span>
                                <span>$1,450.00</span>
                            </div>
                        </div>
                        <div class="totals-right">
                            <div class="grand-total">
                                <div class="grand-label">MONTO TOTAL</div>
                                <div class="grand-amount">$1,450.00</div>
                                <div class="grand-text">
                                    MIL CUATROCIENTOS CINCUENTA PESOS 00/100 MXN
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sello digital y cadena original -->
                <div class="digital-signature">
                    <div class="signature-item">
                        <strong>Sello digital:</strong>
                        <span
                            >asd8f7a9s8d7f9a8s7d6f5a4s3d2f1a5s4d3f2a1s5d4f3g2h1j5k4l3k2j1h5g4f3d2s1a5s4d3f2g1h5j4k3l2</span
                        >
                    </div>
                    <div class="signature-item">
                        <strong>Cadena original:</strong>
                        <span
                            >||we collab|Jose Gonzales
                            Leon|11/04/2026|1250.00|MXN|...</span
                        >
                    </div>
                    <div class="signature-item">
                        <strong>No. de certificado:</strong>
                        <span>00001000000412345678</span>
                    </div>
                </div>
            </div>

            <!-- ================= FOOTER PREMIUM ================= -->
            <div class="footer">
                <div class="footer-main">
                    <div class="footer-message">
                        <div class="thanks-icon">🏆</div>
                        <p class="thanks-message">
                            GRACIAS POR CONFIAR EN WE COLLAB
                        </p>
                        <p class="thanks-submessage">
                            ¡Valoramos tu preferencia y confianza!
                        </p>
                    </div>

                    <div class="footer-divider"></div>

                    <div class="footer-grid">
                        <div class="footer-block">
                            <div class="footer-icon">📍</div>
                            <h4>Dirección fiscal</h4>
                            <p>
                                Primo Feliciano Velazquez 3930<br />Colonia
                                Moderna, Guadalajara, Jalisco<br />Código Postal
                                44500, México
                            </p>
                        </div>

                        <div class="footer-block">
                            <div class="footer-icon">📞</div>
                            <h4>Contacto</h4>
                            <p>
                                Teléfono: +52 33 5121 3546<br />WhatsApp: +52 33
                                5121 3546<br />Atención: L-V 9:00 a 18:00 hrs
                            </p>
                        </div>

                        <div class="footer-block">
                            <div class="footer-icon">✉️</div>
                            <h4>Medios digitales</h4>
                            <p>
                                Email: info@we-collab.tech<br />Sitio web:
                                www.we-collab.tech<br />Soporte:
                                soporte@we-collab.tech
                            </p>
                        </div>

                        <div class="footer-block">
                            <div class="footer-icon">🔒</div>
                            <h4>Verificación CFDI</h4>
                            <p>
                                Consulta en: portalcfdi.we-collab.tech<br />Folio
                                fiscal: WEC-2026-0411-001<br />Estatus: Vigente
                            </p>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="legal-info">
                        <p>© 2026 we collab - Todos los derechos reservados</p>
                        <p>
                            Este comprobante tiene validez fiscal de acuerdo a
                            las disposiciones vigentes
                        </p>
                        <p>
                            Empresa certificada bajo la normativa ISO 9001:2025
                        </p>
                    </div>
                    <div class="qr-code">
                        <div class="qr-placeholder">
                            <span>CÓDIGO QR</span>
                            <span class="qr-small">para verificar CFDI</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const receiptData = ref({
    comprobante: {
        folio: "we collab",
        fecha: "11/04/2026",
        autorizacion: "11/04/2026",
        referencia: "11/04/2026",
    },
    cliente: {
        empresa: "we collab",
        titular: "Jose Gonzales Leon",
        plan: "Jose Gonzales Leon",
    },
    detalle: {
        concepto: "we collab",
        metodo: "we collab",
        banco: "we collab",
        moneda: "we collab",
    },
    total: 1250.0,
});
</script>

<style scoped>
/* ESTILOS TAMAÑO CARTA (8.5" x 11") OPTIMIZADOS PARA PDF */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.receipt-container {
    background: #e5e7eb;
    padding: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.receipt {
    width: 216mm; /* 8.5 pulgadas */
    min-height: 279mm; /* 11 pulgadas */
    background: white;
    margin: 0 auto;
    position: relative;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

/* ========== HEADER PREMIUM ========== */
.header {
    position: relative;
    background: white;
}

.header-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
    height: 28mm;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.banner-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(
        circle at 20% 50%,
        rgba(255, 255, 255, 0.1) 1px,
        transparent 1px
    );
    background-size: 20px 20px;
}

.logo-section {
    position: relative;
    margin-top: -15mm;
    padding-left: 10mm;
    z-index: 2;
}

.logo {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
    display: inline-block;
    padding: 6mm 10mm;
    border-radius: 5mm;
    box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.logo-squares {
    position: relative;
    width: 24mm;
    height: 24mm;
    margin: 0 auto 4mm;
}

.square {
    position: absolute;
    width: 11mm;
    height: 11mm;
    border-radius: 2.5mm;
}

.square-orange {
    background: linear-gradient(135deg, #f97316, #ea580c);
    top: 0;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
}

.square-blue {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    top: 50%;
    right: 0;
    transform: translateY(-50%) translateX(25%) rotate(45deg);
}

.square-purple {
    background: linear-gradient(135deg, #a855f7, #7e22ce);
    bottom: 0;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
}

.square-green {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    top: 50%;
    left: 0;
    transform: translateY(-50%) translateX(-25%) rotate(45deg);
}

.square-cyan {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(45deg);
    width: 9mm;
    height: 9mm;
    z-index: 10;
    box-shadow: 0 0 4mm rgba(6, 182, 212, 0.5);
}

.company-name {
    color: white;
    font-size: 7mm;
    font-weight: 900;
    text-align: center;
    letter-spacing: 0.5mm;
    margin-bottom: 2mm;
}

.company-tagline {
    color: #a5b4fc;
    font-size: 2.5mm;
    text-align: center;
    letter-spacing: 0.5mm;
    font-weight: 600;
}

.title-section {
    position: absolute;
    top: 0;
    right: 0;
    height: 28mm;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-end;
    padding-right: 10mm;
    z-index: 2;
}

.title-badge {
    background: rgba(255, 255, 255, 0.15);
    padding: 1mm 3mm;
    border-radius: 5mm;
    font-size: 2.5mm;
    font-weight: 600;
    color: #c7d2fe;
    letter-spacing: 0.3mm;
    margin-bottom: 2mm;
}

.receipt-title {
    color: white;
    font-size: 9mm;
    font-weight: 700;
    letter-spacing: 1.5mm;
    margin-bottom: 1.5mm;
}

.receipt-subtitle {
    color: #c7d2fe;
    font-size: 2.8mm;
    font-weight: 500;
}

/* ========== BODY PREMIUM ========== */
.body {
    padding: 10mm 10mm 8mm 10mm;
}

.quick-info {
    display: flex;
    justify-content: space-between;
    background: #f8fafc;
    padding: 3mm 5mm;
    border-radius: 2.5mm;
    margin-bottom: 8mm;
    border: 0.3mm solid #e2e8f0;
}

.quick-item {
    display: flex;
    gap: 2.5mm;
}

.quick-label {
    font-weight: 700;
    color: #475569;
    font-size: 2.8mm;
    text-transform: uppercase;
}

.quick-value {
    color: #1e293b;
    font-size: 2.8mm;
    font-family: monospace;
}

.two-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8mm;
    margin-bottom: 9mm;
}

.info-card {
    border: 0.3mm solid #e2e8f0;
    border-radius: 4mm;
    overflow: hidden;
    background: white;
    box-shadow: 0 1mm 1.5mm -0.3mm rgba(0, 0, 0, 0.05);
}

.card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 4mm 5mm;
    border-bottom: 0.5mm solid #4f46e5;
    display: flex;
    align-items: center;
    gap: 2.5mm;
}

.card-icon {
    font-size: 5mm;
}

.card-title {
    font-size: 3.8mm;
    font-weight: 800;
    color: #1e1b4b;
    letter-spacing: 0.3mm;
    margin: 0;
}

.card-body {
    padding: 5mm;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 2.5mm 0;
    border-bottom: 0.3mm solid #f1f5f9;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #64748b;
    font-size: 3mm;
}

.info-value {
    color: #0f172a;
    font-size: 3mm;
    font-weight: 500;
}

.detail-section {
    margin-bottom: 9mm;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 3mm;
    margin-bottom: 5mm;
}

.section-icon {
    font-size: 6mm;
}

.section-title {
    font-size: 4.2mm;
    font-weight: 800;
    color: #1e1b4b;
    letter-spacing: 0.3mm;
    margin: 0;
}

.section-line {
    flex: 1;
    height: 0.5mm;
    background: linear-gradient(90deg, #4f46e5, transparent);
}

.payment-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 6mm;
}

.payment-table th {
    background: #f8fafc;
    padding: 3mm;
    text-align: left;
    font-size: 3mm;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    border-bottom: 0.5mm solid #e2e8f0;
}

.payment-table td {
    padding: 3mm;
    font-size: 3.2mm;
    color: #334155;
    border-bottom: 0.3mm solid #f1f5f9;
}

.empty-row td {
    height: 20mm;
}

.payment-methods {
    background: #f8fafc;
    padding: 5mm;
    border-radius: 3mm;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 3mm;
}

.method-row {
    display: flex;
    justify-content: space-between;
    padding: 1.5mm 0;
}

.method-label {
    font-weight: 700;
    color: #475569;
    font-size: 3mm;
}

.method-value {
    color: #0f172a;
    font-size: 3mm;
}

.totals-section {
    margin-bottom: 8mm;
}

.totals-card {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-radius: 4mm;
    padding: 6mm;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.totals-left {
    flex: 1;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 2mm 0;
    font-size: 3.5mm;
    color: #78350f;
}

.total-highlight {
    font-weight: 800;
    font-size: 4.5mm;
    border-top: 0.5mm solid #fbbf24;
    margin-top: 2mm;
    padding-top: 3mm;
}

.totals-right {
    text-align: right;
    padding-left: 8mm;
    border-left: 0.5mm solid #fbbf24;
}

.grand-label {
    font-size: 3mm;
    font-weight: 700;
    color: #78350f;
    letter-spacing: 0.3mm;
    margin-bottom: 1.5mm;
}

.grand-amount {
    font-size: 10mm;
    font-weight: 900;
    color: #78350f;
    margin-bottom: 2mm;
}

.grand-text {
    font-size: 2.5mm;
    color: #78350f;
    font-weight: 500;
}

.digital-signature {
    background: #f1f5f9;
    padding: 4mm;
    border-radius: 2.5mm;
    font-size: 2.3mm;
    font-family: monospace;
    color: #475569;
}

.signature-item {
    margin-bottom: 2mm;
    word-break: break-all;
}

.signature-item strong {
    font-weight: 700;
    color: #334155;
}

/* ========== FOOTER PREMIUM ========== */
.footer {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: white;
    margin-top: 5mm;
}

.footer-main {
    padding: 9mm 10mm;
}

.footer-message {
    text-align: center;
    margin-bottom: 8mm;
}

.thanks-icon {
    font-size: 10mm;
    margin-bottom: 2.5mm;
}

.thanks-message {
    font-size: 5.5mm;
    font-weight: 800;
    letter-spacing: 1mm;
    margin-bottom: 2mm;
}

.thanks-submessage {
    font-size: 3mm;
    color: #94a3b8;
}

.footer-divider {
    height: 0.3mm;
    background: linear-gradient(90deg, transparent, #334155, transparent);
    margin-bottom: 8mm;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6mm;
    margin-bottom: 8mm;
}

.footer-block {
    text-align: center;
}

.footer-icon {
    font-size: 6mm;
    margin-bottom: 2.5mm;
}

.footer-block h4 {
    font-size: 3.2mm;
    font-weight: 700;
    margin-bottom: 3mm;
    color: #94a3b8;
    letter-spacing: 0.3mm;
}

.footer-block p {
    font-size: 2.6mm;
    color: #cbd5e1;
    line-height: 1.6;
}

.footer-bottom {
    background: rgba(0, 0, 0, 0.2);
    padding: 5mm 10mm;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.legal-info {
    text-align: left;
}

.legal-info p {
    font-size: 2.3mm;
    color: #94a3b8;
    margin-bottom: 1mm;
}

.qr-placeholder {
    width: 15mm;
    height: 15mm;
    background: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 2mm;
    color: #0f172a;
    font-weight: bold;
    text-align: center;
    border-radius: 2mm;
}

.qr-small {
    font-size: 1.5mm;
    font-weight: normal;
}

/* ========== MEDIA PRINT PARA TAMAÑO CARTA ========== */
@media print {
    @page {
        size: letter;
        margin: 0;
    }

    .receipt-container {
        background: white;
        padding: 0;
        margin: 0;
    }

    .receipt {
        width: 100%;
        min-height: auto;
        box-shadow: none;
        margin: 0;
    }

    .header-banner,
    .logo,
    .title-badge,
    .info-card,
    .totals-card,
    .footer {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
