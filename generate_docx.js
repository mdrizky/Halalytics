const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  AlignmentType, HeadingLevel, BorderStyle, WidthType, ShadingType,
  LevelFormat, PageBreak, VerticalAlign
} = require('docx');
const fs = require('fs');

// ── WARNA BRAND HALALYTICS ──
const GREEN     = "1D9E75";
const DARKGREEN = "0A5C42";
const AMBER     = "BA7517";
const RED       = "D85A30";
const BLUE      = "1A6FA8";
const PURPLE    = "6A3BAB";
const LIGHTGRAY = "F5F5F5";
const MIDGRAY   = "E0E0E0";
const DARKGRAY  = "555555";
const WHITE     = "FFFFFF";
const BLACK     = "1A1A1A";

const border = { style: BorderStyle.SINGLE, size: 1, color: MIDGRAY };
const borders = { top: border, bottom: border, left: border, right: border };
const noBorder = { style: BorderStyle.NONE, size: 0, color: WHITE };
const noBorders = { top: noBorder, bottom: noBorder, left: noBorder, right: noBorder };

// ── HELPER PARAGRAPHS ──
const sp = (pts = 6) => new Paragraph({ children: [new TextRun("")] });

const heading1 = (text, color = DARKGREEN) => new Paragraph({
  heading: HeadingLevel.HEADING_1,
  children: [new TextRun({ text, color, bold: true, font: "Arial", size: 34 })]
});

const heading2 = (text, color = DARKGREEN) => new Paragraph({
  heading: HeadingLevel.HEADING_2,
  children: [new TextRun({ text, color, bold: true, font: "Arial", size: 26 })]
});

const heading3 = (text, color = BLUE) => new Paragraph({
  heading: HeadingLevel.HEADING_3,
  children: [new TextRun({ text, color, bold: true, font: "Arial", size: 22 })]
});

const bodyText = (text, opts = {}) => new Paragraph({
  spacing: { after: 120 },
  children: [new TextRun({
    text, font: "Arial", size: 22, color: opts.color || BLACK,
    bold: opts.bold || false, italics: opts.italic || false
  })]
});

const bulletItem = (text, color = BLACK) => new Paragraph({
  numbering: { reference: "bullets", level: 0 },
  spacing: { after: 80 },
  children: [new TextRun({ text, font: "Arial", size: 21, color })]
});

const numberedItem = (text, color = BLACK) => new Paragraph({
  numbering: { reference: "numbers", level: 0 },
  spacing: { after: 80 },
  children: [new TextRun({ text, font: "Arial", size: 21, color })]
});

// ── STATUS BADGE ──
const statusBadge = (label, bgColor, textColor = WHITE) => new Table({
  width: { size: 2400, type: WidthType.DXA },
  columnWidths: [2400],
  rows: [
    new TableRow({
      children: [
        new TableCell({
          borders: noBorders,
          shading: { fill: bgColor, type: ShadingType.CLEAR },
          margins: { top: 80, bottom: 80, left: 160, right: 160 },
          width: { size: 2400, type: WidthType.DXA },
          children: [new Paragraph({
            alignment: AlignmentType.CENTER,
            children: [new TextRun({ text: label, font: "Arial", size: 20, bold: true, color: textColor })]
          })]
        })
      ]
    })
  ]
});

// ── KOTAK INFO ──
const infoBox = (title, lines, bgColor = "EBF5EF", borderColor = GREEN) => {
  const cells = [];
  cells.push(new Paragraph({
    spacing: { after: 80 },
    children: [new TextRun({ text: title, font: "Arial", size: 22, bold: true, color: DARKGREEN })]
  }));
  lines.forEach(l => {
    if (typeof l === 'string') {
      cells.push(new Paragraph({
        spacing: { after: 60 },
        children: [new TextRun({ text: l, font: "Arial", size: 21, color: DARKGRAY })]
      }));
    }
  });
  return new Table({
    width: { size: 9360, type: WidthType.DXA },
    columnWidths: [9360],
    rows: [new TableRow({
      children: [new TableCell({
        borders: {
          top: { style: BorderStyle.SINGLE, size: 6, color: borderColor },
          bottom: { style: BorderStyle.SINGLE, size: 1, color: borderColor },
          left: { style: BorderStyle.SINGLE, size: 6, color: borderColor },
          right: { style: BorderStyle.SINGLE, size: 1, color: borderColor }
        },
        shading: { fill: bgColor, type: ShadingType.CLEAR },
        margins: { top: 160, bottom: 160, left: 200, right: 200 },
        width: { size: 9360, type: WidthType.DXA },
        children: cells
      })]
    })]
  });
};

// ── TABEL 2 KOLOM ──
const twoColTable = (rows, col1Width = 3000) => {
  const col2Width = 9360 - col1Width;
  return new Table({
    width: { size: 9360, type: WidthType.DXA },
    columnWidths: [col1Width, col2Width],
    rows: rows.map((row, i) => new TableRow({
      children: [
        new TableCell({
          borders,
          shading: { fill: i === 0 ? DARKGREEN : (i % 2 === 0 ? "F0FAF6" : WHITE), type: ShadingType.CLEAR },
          margins: { top: 80, bottom: 80, left: 120, right: 120 },
          width: { size: col1Width, type: WidthType.DXA },
          children: [new Paragraph({
            children: [new TextRun({
              text: row[0], font: "Arial", size: 21,
              bold: i === 0, color: i === 0 ? WHITE : DARKGREEN
            })]
          })]
        }),
        new TableCell({
          borders,
          shading: { fill: i === 0 ? DARKGREEN : (i % 2 === 0 ? "F0FAF6" : WHITE), type: ShadingType.CLEAR },
          margins: { top: 80, bottom: 80, left: 120, right: 120 },
          width: { size: col2Width, type: WidthType.DXA },
          children: [new Paragraph({
            children: [new TextRun({
              text: row[1], font: "Arial", size: 21,
              bold: i === 0, color: i === 0 ? WHITE : BLACK
            })]
          })]
        })
      ]
    }))
  });
};

// ── TABEL 3 KOLOM ──
const threeColTable = (rows) => {
  const w = [3000, 3000, 3360];
  return new Table({
    width: { size: 9360, type: WidthType.DXA },
    columnWidths: w,
    rows: rows.map((row, i) => new TableRow({
      children: row.map((cell, j) => new TableCell({
        borders,
        shading: { fill: i === 0 ? DARKGREEN : (i % 2 === 0 ? "F0FAF6" : WHITE), type: ShadingType.CLEAR },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        width: { size: w[j], type: WidthType.DXA },
        children: [new Paragraph({
          children: [new TextRun({
            text: cell, font: "Arial", size: 21,
            bold: i === 0, color: i === 0 ? WHITE : (j === 2 ? GREEN : BLACK)
          })]
        })]
      }))
    }))
  });
};

// ─────────────────────────────────────────────────────────────
// DOKUMEN UTAMA
// ─────────────────────────────────────────────────────────────
const doc = new Document({
  numbering: {
    config: [
      {
        reference: "bullets",
        levels: [{ level: 0, format: LevelFormat.BULLET, text: "\u2022", alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } }]
      },
      {
        reference: "numbers",
        levels: [{ level: 0, format: LevelFormat.DECIMAL, text: "%1.", alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } }]
      }
    ]
  },
  styles: {
    default: { document: { run: { font: "Arial", size: 22, color: BLACK } } },
    paragraphStyles: [
      { id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 34, bold: true, font: "Arial" },
        paragraph: { spacing: { before: 400, after: 200 }, outlineLevel: 0 } },
      { id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 28, bold: true, font: "Arial" },
        paragraph: { spacing: { before: 320, after: 160 }, outlineLevel: 1 } },
      { id: "Heading3", name: "Heading 3", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 24, bold: true, font: "Arial" },
        paragraph: { spacing: { before: 240, after: 120 }, outlineLevel: 2 } },
    ]
  },
  sections: [{
    properties: {
      page: {
        size: { width: 12240, height: 15840 },
        margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 }
      }
    },
    children: [

      // ═══════════════════════════════════════════
      // HALAMAN JUDUL
      // ═══════════════════════════════════════════
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 1200, after: 200 },
        children: [new TextRun({ text: "HALALYTICS", font: "Arial", size: 64, bold: true, color: DARKGREEN })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 160 },
        children: [new TextRun({ text: "AI Product Intelligence", font: "Arial", size: 32, color: GREEN })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 600 },
        children: [new TextRun({ text: "Dokumen Teknis & Penjelasan Sistem Lengkap", font: "Arial", size: 24, italics: true, color: DARKGRAY })]
      }),

      // Garis pembatas
      new Paragraph({
        spacing: { after: 400 },
        border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: GREEN, space: 1 } },
        children: [new TextRun("")]
      }),

      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 160 },
        children: [new TextRun({ text: "Versi 1.0  |  2025", font: "Arial", size: 22, color: DARKGRAY })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 800 },
        children: [new TextRun({ text: "Platform Web & Android", font: "Arial", size: 22, color: DARKGRAY })]
      }),

      infoBox("Tentang Dokumen Ini", [
        "Dokumen ini menjelaskan secara lengkap arsitektur teknis, alur kerja sistem, dan implementasi",
        "kode untuk aplikasi Halalytics — platform cerdas berbasis AI yang membantu konsumen Muslim",
        "Indonesia memverifikasi kehalalan produk, menganalisis kandungan bahan, menilai skor kesehatan,",
        "menyajikan informasi nutrisi transparan, dan merekomendasikan alternatif produk yang lebih sehat."
      ]),

      sp(20),

      new Paragraph({ children: [new PageBreak()] }),

      // ═══════════════════════════════════════════
      // BAB 1: LATAR BELAKANG & VISI
      // ═══════════════════════════════════════════
      heading1("BAB 1 — Latar Belakang & Visi Aplikasi"),
      sp(),
      bodyText("Indonesia adalah negara dengan populasi Muslim terbesar di dunia. Lebih dari 87% penduduk Indonesia beragama Islam, menjadikan kehalalan produk sebagai kebutuhan primer dalam kehidupan sehari-hari. Namun, tantangan nyata yang dihadapi konsumen adalah:"),
      sp(4),
      bulletItem("Label produk sering tidak transparan atau menggunakan istilah kimia yang sulit dipahami awam"),
      bulletItem("Proses sertifikasi halal BPJPH/MUI tidak mencakup semua produk yang beredar di pasaran"),
      bulletItem("Konsumen kesulitan membedakan bahan yang jelas haram, meragukan (syubhat), dan aman"),
      bulletItem("Informasi nilai gizi sulit dibaca dan tidak dihubungkan dengan dampak kesehatan nyata"),
      bulletItem("Tidak ada platform terpadu yang menggabungkan analisis halal sekaligus penilaian kesehatan"),
      sp(8),
      heading2("1.1  Visi Halalytics"),
      bodyText("Halalytics hadir sebagai solusi satu platform untuk pengambilan keputusan tepat bagi konsumen Muslim Indonesia. Dengan teknologi AI dan database produk global, Halalytics mampu menganalisis produk hanya dari scan barcode dalam hitungan detik."),
      sp(8),
      infoBox("Motto Aplikasi", [
        "\"Satu Platform, Keputusan Tepat.\"",
        "",
        "Halalytics menggabungkan kecerdasan buatan (AI), database produk global Open Food Facts,",
        "dan database bahan haram lokal untuk memberikan analisis lengkap: status halal, kandungan",
        "bahan, skor kesehatan, informasi nutrisi, dan rekomendasi alternatif produk — semuanya gratis."
      ]),
      sp(16),

      // ═══════════════════════════════════════════
      // BAB 2: LIMA SOLUSI UTAMA
      // ═══════════════════════════════════════════
      heading1("BAB 2 — Lima Solusi Utama Halalytics"),
      sp(),
      bodyText("Berdasarkan kebutuhan konsumen dan analisis masalah yang ada, Halalytics dirancang dengan lima pilar solusi utama yang saling terintegrasi:"),
      sp(8),

      twoColTable([
        ["No.", "Solusi"],
        ["Solusi 1", "Status Kehalalan Produk — cek halal/syubhat/haram dari bahan produk"],
        ["Solusi 2", "Analisis Kandungan Bahan — penjelasan fungsi dan risiko tiap bahan"],
        ["Solusi 3", "Penilaian Skor Kesehatan — skor 0-100 berdasarkan Nova, Nutri-Score, dan gizi"],
        ["Solusi 4", "Informasi Nutrisi Transparan — tabel nutrisi lengkap dengan indikator visual"],
        ["Solusi 5", "Rekomendasi Alternatif Sehat — saran produk pengganti yang lebih baik"],
      ], 1800),
      sp(12),

      heading2("2.1  Solusi 1 — Status Kehalalan Produk"),
      bodyText("Solusi ini adalah fitur paling kritis Halalytics. Karena API Open Food Facts tidak menyediakan data kehalalan, sistem menggunakan dua lapisan analisis:"),
      sp(4),
      bulletItem("Lapisan 1 — Database Lokal Offline: Daftar bahan haram dan syubhat yang dikurasi dari fatwa MUI dan standar BPJPH. Pengecekan ini berlangsung instan tanpa internet."),
      bulletItem("Lapisan 2 — AI Groq (LLaMA 3.3 70B): Untuk bahan-bahan yang tidak ada di database lokal atau membutuhkan konteks lebih dalam, sistem memanggil AI untuk analisis lanjutan."),
      sp(4),
      bodyText("Hasil analisis dikategorikan menjadi tiga status:"),
      sp(4),

      new Table({
        width: { size: 9360, type: WidthType.DXA },
        columnWidths: [2000, 3000, 4360],
        rows: [
          new TableRow({ children: [
            new TableCell({ borders, shading: { fill: DARKGREEN, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 2000, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Status", font: "Arial", size: 21, bold: true, color: WHITE })] })] }),
            new TableCell({ borders, shading: { fill: DARKGREEN, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 3000, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Arti", font: "Arial", size: 21, bold: true, color: WHITE })] })] }),
            new TableCell({ borders, shading: { fill: DARKGREEN, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 4360, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Contoh Bahan Pemicu", font: "Arial", size: 21, bold: true, color: WHITE })] })] }),
          ]}),
          new TableRow({ children: [
            new TableCell({ borders, shading: { fill: "E8F8F2", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 2000, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "HALAL", font: "Arial", size: 21, bold: true, color: GREEN })] })] }),
            new TableCell({ borders, shading: { fill: "E8F8F2", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 3000, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Tidak ditemukan bahan bermasalah", font: "Arial", size: 21, color: BLACK })] })] }),
            new TableCell({ borders, shading: { fill: "E8F8F2", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 4360, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Garam, gula, tepung, minyak sawit, dll.", font: "Arial", size: 21, color: DARKGRAY })] })] }),
          ]}),
          new TableRow({ children: [
            new TableCell({ borders, shading: { fill: "FEF6E7", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 2000, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "SYUBHAT", font: "Arial", size: 21, bold: true, color: AMBER })] })] }),
            new TableCell({ borders, shading: { fill: "FEF6E7", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 3000, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Ada bahan yang sumbernya meragukan", font: "Arial", size: 21, color: BLACK })] })] }),
            new TableCell({ borders, shading: { fill: "FEF6E7", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 4360, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Gelatin, E471, lesitin, perisa alami, dll.", font: "Arial", size: 21, color: DARKGRAY })] })] }),
          ]}),
          new TableRow({ children: [
            new TableCell({ borders, shading: { fill: "FDECEA", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 2000, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "HARAM", font: "Arial", size: 21, bold: true, color: RED })] })] }),
            new TableCell({ borders, shading: { fill: "FDECEA", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 3000, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Ditemukan bahan yang jelas haram", font: "Arial", size: 21, color: BLACK })] })] }),
            new TableCell({ borders, shading: { fill: "FDECEA", type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 4360, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Babi, alkohol, wine, darah, carmine/E120", font: "Arial", size: 21, color: DARKGRAY })] })] }),
          ]}),
        ]
      }),
      sp(12),

      heading2("2.2  Solusi 2 — Analisis Kandungan Bahan"),
      bodyText("Fitur ini menjelaskan setiap bahan dalam produk secara mudah dipahami. AI (Groq LLaMA) menganalisis daftar bahan dan memberikan penjelasan untuk setiap bahan utama:"),
      sp(4),
      bulletItem("Fungsi bahan dalam produk (contoh: pengawet, pewarna, pemanis, pengemulsi)"),
      bulletItem("Risiko kesehatan jika dikonsumsi berlebihan atau untuk kelompok tertentu"),
      bulletItem("Level keamanan: AMAN (hijau), PERHATIAN (kuning), atau HINDARI (merah)"),
      sp(4),
      bodyText("Dengan fitur ini, konsumen tidak perlu memahami nama kimia yang rumit. Cukup lihat warna indikator dan deskripsi singkat untuk membuat keputusan cepat."),
      sp(12),

      heading2("2.3  Solusi 3 — Penilaian Skor Kesehatan"),
      bodyText("Skor kesehatan dihitung 100% secara lokal (offline) menggunakan formula yang menggabungkan tiga faktor utama:"),
      sp(4),
      twoColTable([
        ["Faktor", "Penjelasan & Bobot Penilaian"],
        ["Nova Group (1-4)", "Tingkat pemrosesan produk. Nova 1 = alami (0 poin pengurangan), Nova 4 = ultra-proses (-30 poin)"],
        ["Nutri-Score (A-E)", "Profil nutrisi keseluruhan. Grade A = terbaik (+5 poin), Grade E = terburuk (-30 poin)"],
        ["Nilai Gizi Aktual", "Gula, lemak jenuh, garam, serat, protein dievaluasi per 100g produk"],
        ["Hasil Skor", "0-100. Skor >= 70: Sehat (hijau). 45-69: Cukup Sehat (kuning). < 45: Tidak Sehat (merah)"],
      ], 2400),
      sp(12),

      heading2("2.4  Solusi 4 — Informasi Nutrisi Transparan"),
      bodyText("Data nutrisi diambil langsung dari Open Food Facts dan disajikan dalam format yang mudah dipahami. Setiap nilai nutrisi dilengkapi dengan:"),
      sp(4),
      bulletItem("Nilai aktual per 100g dalam satuan yang jelas (gram, kkal)"),
      bulletItem("Indikator warna visual: hijau (normal), kuning (perhatian), merah (berlebih)"),
      bulletItem("Mini progress bar untuk visualisasi cepat level tiap nutrisi"),
      bulletItem("Badge Nutri-Score (A/B/C/D/E) dan Nova Group (1/2/3/4)"),
      sp(12),

      heading2("2.5  Solusi 5 — Rekomendasi Alternatif Sehat"),
      bodyText("Ketika produk memiliki skor kesehatan rendah, status haram/syubhat, atau kandungan berbahaya, AI akan merekomendasikan alternatif produk yang lebih baik. Rekomendasi bersifat:"),
      sp(4),
      bulletItem("Kontekstual — disesuaikan dengan jenis kelemahan produk (gula tinggi, lemak jenuh, dll.)"),
      bulletItem("Lokal — fokus pada produk yang mudah ditemukan di supermarket Indonesia"),
      bulletItem("Halal — semua rekomendasi dipastikan aman dari sisi kehalalan"),
      sp(4),
      bodyText("Di akhir setiap analisis, sistem memberikan Verdict Akhir yang tegas: DIREKOMENDASIKAN atau TIDAK DIREKOMENDASIKAN, berdasarkan gabungan skor kesehatan dan status halal."),
      sp(16),

      // ═══════════════════════════════════════════
      // BAB 3: ARSITEKTUR TEKNIS & API
      // ═══════════════════════════════════════════
      heading1("BAB 3 — Arsitektur Teknis & API yang Digunakan"),
      sp(),
      heading2("3.1  Stack Teknologi"),
      twoColTable([
        ["Komponen", "Teknologi"],
        ["Platform Utama", "Android (Jetpack Compose) + Web"],
        ["Bahasa Pemrograman", "Kotlin (Android) / JavaScript (Web)"],
        ["UI Framework", "Jetpack Compose (Material 3)"],
        ["Arsitektur", "MVVM (Model-View-ViewModel) + Repository Pattern"],
        ["Async/Coroutine", "Kotlin Coroutines + viewModelScope"],
        ["HTTP Client", "OkHttp3 / Retrofit2"],
        ["Database Lokal", "Room Database (SQLite)"],
        ["Scan Barcode", "ML Kit Google (Google's Barcode API)"],
        ["State Management", "mutableStateOf + StateFlow"],
      ], 3000),
      sp(12),

      heading2("3.2  API Eksternal yang Digunakan (Semua Gratis)"),
      sp(4),
      threeColTable([
        ["API / Layanan", "Fungsi", "Biaya"],
        ["Open Food Facts API", "Data produk, nutrisi, bahan, foto", "GRATIS — No API Key"],
        ["Groq API (LLaMA 3.3 70B)", "AI analisis halal, kandungan, rekomendasi", "GRATIS — 30 req/mnt"],
        ["ML Kit Google", "Scan barcode offline di kamera", "GRATIS — Offline"],
        ["Room Database", "Database bahan haram lokal", "GRATIS — Lokal"],
        ["Kalkulasi Lokal", "Skor kesehatan, Nova, Nutri-Score", "GRATIS — Offline"],
        ["TOTAL BIAYA", "Seluruh fitur aplikasi", "Rp 0"],
      ]),
      sp(8),
      infoBox("Cara Mendapatkan Groq API Key (Gratis)", [
        "1. Buka: console.groq.com",
        "2. Daftar dengan akun Google atau GitHub (gratis, tidak perlu kartu kredit)",
        "3. Klik menu 'API Keys' di sidebar kiri",
        "4. Klik 'Create API Key', beri nama, lalu copy key yang dihasilkan",
        "5. Simpan di file local.properties: GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxx",
        "6. JANGAN commit file local.properties ke Git (tambahkan ke .gitignore)"
      ], "EBF5EF", GREEN),
      sp(12),

      heading2("3.3  Alur Sistem Lengkap (End-to-End)"),
      sp(4),
      bodyText("Berikut adalah alur kerja lengkap dari saat user membuka kamera hingga hasil analisis ditampilkan di layar:"),
      sp(4),
      numberedItem("User membuka fitur Scan di aplikasi Halalytics"),
      numberedItem("Kamera aktif — ML Kit mendeteksi dan mendekode barcode secara real-time"),
      numberedItem("Setelah barcode berhasil di-scan, nomor barcode dikirim ke ProductRepository"),
      numberedItem("Repository memanggil Open Food Facts API untuk mengambil data produk (nama, bahan, nutrisi, foto)"),
      numberedItem("Sistem menjalankan analisis paralel menggunakan Kotlin Coroutines (async/await):"),
      sp(4),
      bulletItem("Analisis Halal: cek DB lokal terlebih dahulu (offline, instan). Jika bahan tidak dikenal, lanjut ke Groq AI"),
      bulletItem("Analisis Kandungan: Groq AI menjelaskan fungsi dan risiko setiap bahan utama"),
      bulletItem("Skor Kesehatan: kalkulasi lokal menggunakan Nova Group + Nutri-Score + nilai gizi aktual"),
      bulletItem("Informasi Nutrisi: langsung dari data Open Food Facts, ditampilkan dengan indikator warna"),
      bulletItem("Rekomendasi Alternatif: Groq AI merekomendasikan produk pengganti berdasarkan kelemahan produk"),
      sp(4),
      numberedItem("Semua hasil dikumpulkan menjadi satu objek ProductDetail"),
      numberedItem("ViewModel menerima data dan mengubah UI State"),
      numberedItem("Layar Detail Produk ditampilkan dengan semua informasi tersusun rapi dalam satu halaman"),
      numberedItem("Verdict Akhir ditampilkan di paling bawah: DIREKOMENDASIKAN atau TIDAK DIREKOMENDASIKAN"),
      sp(12),

      heading2("3.4  Struktur Proyek Android"),
      sp(4),
      infoBox("Struktur Folder Proyek (Jetpack Compose)", [
        "app/src/main/java/com/halalytics/",
        "|-- data/",
        "|   |-- LocalHaramDatabase.kt     <- List bahan haram/syubhat (offline)",
        "|   |-- OpenFoodFactsApi.kt       <- Retrofit API client",
        "|   `-- GroqApiClient.kt          <- Pemanggil Groq AI",
        "|-- model/",
        "|   |-- ProductDetail.kt          <- Data class utama produk",
        "|   |-- HalalStatus.kt            <- Sealed class: HALAL/SYUBHAT/HARAM",
        "|   |-- HealthScore.kt            <- Data class skor kesehatan",
        "|   `-- IngredientDetail.kt       <- Data class analisis per bahan",
        "|-- repository/",
        "|   `-- ProductRepository.kt     <- Logika gabungan semua analisis",
        "|-- viewmodel/",
        "|   `-- ProductViewModel.kt      <- State management + coroutine",
        "`-- ui/",
        "    |-- ScanScreen.kt            <- Layar scan kamera",
        "    |-- ProductDetailScreen.kt   <- Layar detail produk (utama)",
        "    `-- components/              <- Komponen UI reusable"
      ], "EEF2FF", PURPLE),
      sp(16),

      // ═══════════════════════════════════════════
      // BAB 4: PENJELASAN KODE DETAIL
      // ═══════════════════════════════════════════
      heading1("BAB 4 — Penjelasan Kode per Komponen"),
      sp(),

      heading2("4.1  Database Lokal Bahan Haram (LocalHaramDatabase.kt)"),
      bodyText("Komponen ini adalah jantung dari fitur analisis halal. Database lokal dibuat sebagai Kotlin Object (singleton) yang berisi tiga daftar utama:"),
      sp(4),
      bulletItem("haramList: Bahan yang PASTI haram berdasarkan fatwa MUI. Contoh: babi, pork, lard, alkohol, wine, beer, darah, carmine (E120), enzim babi."),
      bulletItem("syubhatList: Bahan yang MERAGUKAN karena sumbernya bisa dari hewan halal atau haram. Contoh: gelatin, E471, E472, lesitin, whey, casein, perisa alami, L-cysteine (E920)."),
      sp(4),
      bodyText("Fungsi analyzeIngredients() menerima teks bahan produk, mengubahnya menjadi huruf kecil (lowercase), lalu mencocokkan kata per kata dengan kedua daftar. Prioritas pengecekan adalah haram terlebih dahulu. Jika ada satu saja bahan haram ditemukan, langsung return HalalStatus.HARAM tanpa perlu cek lebih lanjut."),
      sp(4),
      infoBox("Kenapa Perlu DB Lokal? Bukan Langsung AI Saja?", [
        "Pengecekan DB lokal berlangsung dalam milidetik tanpa koneksi internet, sedangkan",
        "pemanggilan AI membutuhkan waktu 1-3 detik dan menggunakan kuota API. Dengan strategi",
        "dua lapisan ini, 80%+ kasus dapat diselesaikan secara lokal, menghemat kuota Groq API",
        "untuk kasus-kasus yang benar-benar membutuhkan analisis lebih mendalam."
      ], "FEF6E7", AMBER),
      sp(12),

      heading2("4.2  Repository (ProductRepository.kt)"),
      bodyText("Repository adalah pusat koordinasi semua analisis. Pola desain yang digunakan adalah Repository Pattern, yang memisahkan logika bisnis dari UI. Fitur paling penting adalah penggunaan Kotlin Coroutines dengan async/await untuk menjalankan beberapa analisis secara paralel (bersamaan), bukan berurutan."),
      sp(4),
      bodyText("Contoh: Jika analisis halal membutuhkan 2 detik dan analisis kandungan membutuhkan 2 detik, dengan pemrosesan berurutan total waktu adalah 4 detik. Dengan async paralel, keduanya berjalan bersamaan sehingga total waktu hanya 2 detik — dua kali lebih cepat."),
      sp(4),
      infoBox("Alur Kerja Repository", [
        "1. Panggil Open Food Facts API -> dapatkan data produk mentah",
        "2. Parse data nutrisi dari format Map<String, Any> ke objek Nutriments",
        "3. Jalankan PARALEL dengan async { ... }:",
        "   a. analyzeHalal() -> cek DB lokal, lalu AI jika perlu",
        "   b. analyzeIngredientDetails() -> AI analisis tiap bahan",
        "4. Jalankan LOKAL (tidak perlu async):",
        "   c. HealthScoreCalculator.calculate() -> hitung skor offline",
        "5. Tunggu semua hasil dengan .await()",
        "6. Panggil getAlternatives() dengan hasil analisis sebelumnya",
        "7. Gabungkan semua ke objek ProductDetail dan return"
      ], "EEF2FF", PURPLE),
      sp(12),

      heading2("4.3  ViewModel (ProductViewModel.kt)"),
      bodyText("ViewModel bertugas menjembatani Repository dengan UI. Menggunakan Kotlin mutableStateOf untuk state reaktif yang otomatis memicu rekomposisi UI ketika data berubah. UI State didefinisikan sebagai sealed class dengan empat kemungkinan:"),
      sp(4),
      twoColTable([
        ["UI State", "Kapan Terjadi"],
        ["Idle", "Awal saat layar pertama dibuka, belum ada aksi"],
        ["Loading", "Setelah barcode di-scan, sedang menunggu hasil analisis"],
        ["Success(product)", "Analisis selesai, data ProductDetail siap ditampilkan"],
        ["Error(message)", "Terjadi kesalahan: produk tidak ditemukan, tidak ada internet, dll."],
      ], 2200),
      sp(12),

      heading2("4.4  Layar Detail Produk (ProductDetailScreen.kt)"),
      bodyText("Layar ini dibangun dengan LazyColumn Jetpack Compose yang menampilkan semua kartu informasi secara berurutan. Setiap bagian dikemas dalam Card yang terpisah untuk keterbacaan yang baik. Urutan tampilan:"),
      sp(4),
      numberedItem("Header Produk — Foto produk (dari URL Open Food Facts), nama produk, merek, dan barcode"),
      numberedItem("Kartu Status Halal — Badge berwarna besar (hijau/kuning/merah) dengan alasan dan bahan bermasalah"),
      numberedItem("Kartu Skor Kesehatan — Circular progress bar animasi dengan skor dan daftar poin plus/minus"),
      numberedItem("Kartu Analisis AI — Penjelasan singkat dari AI tentang produk dalam bahasa Indonesia"),
      numberedItem("Kartu Analisis Kandungan — List bahan dengan warna indikator dan penjelasan fungsi/risiko"),
      numberedItem("Kartu Nutrisi Transparan — Tabel lengkap dengan progress bar dan badge Nutri-Score/Nova"),
      numberedItem("Kartu Rekomendasi Alternatif — Daftar produk pengganti yang lebih sehat dan halal"),
      numberedItem("Kartu Verdict Akhir — Kesimpulan final: DIREKOMENDASIKAN atau TIDAK DIREKOMENDASIKAN"),
      sp(16),

      // ═══════════════════════════════════════════
      // BAB 5: KALKULASI SKOR KESEHATAN
      // ═══════════════════════════════════════════
      heading1("BAB 5 — Formula Penilaian Skor Kesehatan"),
      sp(),
      bodyText("Skor kesehatan Halalytics dihitung dengan sistem poin yang mempertimbangkan tujuh faktor. Skor dimulai dari 100 dan dikurangi atau ditambah berdasarkan setiap faktor:"),
      sp(8),

      new Table({
        width: { size: 9360, type: WidthType.DXA },
        columnWidths: [2800, 2200, 4360],
        rows: [
          new TableRow({ children: [
            new TableCell({ borders, shading: { fill: DARKGREEN, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 2800, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Faktor", font: "Arial", size: 21, bold: true, color: WHITE })] })] }),
            new TableCell({ borders, shading: { fill: DARKGREEN, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 2200, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Perubahan Skor", font: "Arial", size: 21, bold: true, color: WHITE })] })] }),
            new TableCell({ borders, shading: { fill: DARKGREEN, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 4360, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: "Detail", font: "Arial", size: 21, bold: true, color: WHITE })] })] }),
          ]}),
          ...[
            ["Nova Group 1 (alami)", "0 (tetap)", "Tidak ada pengurangan, kategori makanan alami"],
            ["Nova Group 2", "-5 poin", "Produk kuliner olahan ringan"],
            ["Nova Group 3", "-15 poin", "Makanan olahan"],
            ["Nova Group 4 (ultra-proses)", "-30 poin", "Sangat banyak aditif dan pemrosesan kimia"],
            ["Nutri-Score A", "+5 poin", "Profil nutrisi terbaik"],
            ["Nutri-Score B/C", "0 / -10 poin", "Cukup baik / rata-rata"],
            ["Nutri-Score D/E", "-20 / -30 poin", "Buruk / sangat buruk"],
            ["Gula > 30g/100g", "-20 poin", "Kadar gula sangat tinggi, risiko diabetes"],
            ["Gula 15-30g/100g", "-10 poin", "Kadar gula cukup tinggi"],
            ["Lemak jenuh > 10g", "-15 poin", "Risiko kolesterol dan penyakit jantung"],
            ["Garam > 2.5g/100g", "-15 poin", "Risiko hipertensi"],
            ["Serat > 6g/100g", "+10 poin", "Sangat baik untuk pencernaan"],
            ["Serat 3-6g/100g", "+5 poin", "Cukup baik untuk kesehatan usus"],
            ["Protein > 10g/100g", "+5 poin", "Sumber protein yang baik"],
          ].map(([a, b, c], i) => new TableRow({ children: [
            new TableCell({ borders, shading: { fill: i % 2 === 0 ? "F0FAF6" : WHITE, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 2800, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: a, font: "Arial", size: 21, color: DARKGREEN })] })] }),
            new TableCell({ borders, shading: { fill: i % 2 === 0 ? "F0FAF6" : WHITE, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 2200, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: b, font: "Arial", size: 21, bold: true, color: b.startsWith('+') ? GREEN : b === '0 (tetap)' ? DARKGRAY : RED })] })] }),
            new TableCell({ borders, shading: { fill: i % 2 === 0 ? "F0FAF6" : WHITE, type: ShadingType.CLEAR }, margins: { top: 80, bottom: 80, left: 120, right: 120 }, width: { size: 4360, type: WidthType.DXA }, children: [new Paragraph({ children: [new TextRun({ text: c, font: "Arial", size: 21, color: BLACK })] })] }),
          ]}))
        ]
      }),
      sp(12),
      infoBox("Interpretasi Hasil Skor", [
        "SEHAT (Skor 70-100)       : Hijau  — Aman dikonsumsi secara rutin",
        "CUKUP SEHAT (Skor 45-69) : Kuning — Konsumsi wajar, perhatikan porsi",
        "TIDAK SEHAT (Skor 0-44)  : Merah  — Hindari konsumsi rutin, cari alternatif"
      ], "EBF5EF", GREEN),
      sp(16),

      // ═══════════════════════════════════════════
      // BAB 6: PENJELASAN OPEN FOOD FACTS API
      // ═══════════════════════════════════════════
      heading1("BAB 6 — Open Food Facts API"),
      sp(),
      bodyText("Open Food Facts adalah database produk makanan global yang bersifat open source dan sepenuhnya gratis. Tidak membutuhkan API key. Database ini memiliki lebih dari 3 juta produk dari seluruh dunia, termasuk banyak produk yang dijual di Indonesia."),
      sp(8),
      heading2("6.1  Endpoint yang Digunakan"),
      infoBox("URL API", [
        "GET https://world.openfoodfacts.org/api/v2/product/{BARCODE}",
        "",
        "?fields=product_name,brands,image_url,ingredients_text,",
        "        nutriments,nova_group,nutriscore_grade",
        "",
        "Contoh:",
        "GET https://world.openfoodfacts.org/api/v2/product/8996001302347"
      ], "EEF2FF", BLUE),
      sp(8),
      heading2("6.2  Data yang Tersedia dari API"),
      twoColTable([
        ["Field API", "Keterangan"],
        ["product_name", "Nama produk seperti yang tercetak di kemasan"],
        ["brands", "Nama merek/brand produk"],
        ["image_url", "URL foto produk (dari kontribusi komunitas)"],
        ["ingredients_text", "Teks daftar bahan lengkap (kunci untuk analisis halal)"],
        ["nutriments", "Nilai gizi per 100g: kalori, lemak, gula, garam, protein, serat, dll."],
        ["nova_group", "Tingkat pemrosesan 1-4 (1=alami, 4=ultra-proses)"],
        ["nutriscore_grade", "Nilai nutrisi A/B/C/D/E (A=terbaik, E=terburuk)"],
        ["categories", "Kategori produk (biskuit, minuman, daging, dll.)"],
      ], 2600),
      sp(8),
      heading2("6.3  Batasan & Solusi"),
      bodyText("Open Food Facts mengandalkan kontribusi komunitas, sehingga tidak semua produk tersedia, terutama produk lokal Indonesia yang kurang dikenal. Berikut strategi menanganinya:"),
      sp(4),
      bulletItem("Jika produk tidak ditemukan: tampilkan pesan ramah dan saran untuk memindai ulang atau mencari manual"),
      bulletItem("Jika bahan (ingredients_text) kosong: analisis halal langsung menggunakan AI berdasarkan nama produk"),
      bulletItem("Jika nutrisi tidak lengkap: tampilkan tanda '?' untuk nilai yang tidak tersedia, skor kesehatan dihitung dari data yang ada"),
      bulletItem("Kontribusi balik: pengguna dapat didorong untuk menambahkan produk ke database Open Food Facts untuk membantu komunitas"),
      sp(16),

      // ═══════════════════════════════════════════
      // BAB 7: GROQ AI
      // ═══════════════════════════════════════════
      heading1("BAB 7 — Groq AI & Prompt Engineering"),
      sp(),
      bodyText("Groq adalah platform inferensi AI yang sangat cepat (menggunakan chip LPU khusus) dengan tier gratis yang sangat generous. Model yang digunakan adalah LLaMA 3.3 70B — salah satu model open source terbaik yang tersedia."),
      sp(8),
      twoColTable([
        ["Spesifikasi Groq Free Tier", "Detail"],
        ["Model", "llama-3.3-70b-versatile"],
        ["Rate Limit", "30 request per menit"],
        ["Token per Menit", "6.000 token/menit"],
        ["Token per Hari", "500.000 token/hari"],
        ["Harga", "Rp 0 (gratis selamanya untuk tier free)"],
        ["Kecepatan", "200-400 token/detik (sangat cepat)"],
        ["Cara Daftar", "console.groq.com — daftar dengan Google/GitHub"],
      ], 3200),
      sp(8),
      heading2("7.1  Tiga Prompt Utama Halalytics"),
      sp(4),
      bodyText("PROMPT 1 — Analisis Status Halal:"),
      sp(2),
      infoBox("Prompt Halal", [
        "Kamu adalah ahli halal food Indonesia.",
        "Analisis bahan-bahan produk berikut:",
        "Produk: [nama produk]",
        "Bahan: [daftar bahan]",
        "",
        "Jawab HANYA dengan format JSON:",
        "{",
        "  \"status\": \"HALAL\" atau \"SYUBHAT\" atau \"HARAM\",",
        "  \"alasan\": \"penjelasan singkat 1 kalimat\",",
        "  \"bahan_masalah\": [\"list bahan bermasalah\"]",
        "}"
      ], "FEF6E7", AMBER),
      sp(8),
      bodyText("PROMPT 2 — Analisis Kandungan Bahan:"),
      sp(2),
      infoBox("Prompt Kandungan", [
        "Analisis bahan-bahan makanan berikut untuk konsumen awam Indonesia:",
        "[daftar bahan]",
        "",
        "Untuk setiap bahan UTAMA (max 8), berikan JSON array:",
        "[{",
        "  \"nama\": \"nama bahan\",",
        "  \"fungsi\": \"fungsi dalam produk (1 kata)\",",
        "  \"risiko\": \"risiko atau 'aman'\",",
        "  \"level\": \"AMAN\" atau \"PERHATIAN\" atau \"HINDARI\"",
        "}]"
      ], "EEF2FF", BLUE),
      sp(8),
      bodyText("PROMPT 3 — Rekomendasi Alternatif:"),
      sp(2),
      infoBox("Prompt Rekomendasi", [
        "Produk: [nama produk]",
        "Masalah: [daftar masalah produk]",
        "",
        "Berikan 3 rekomendasi alternatif yang lebih sehat dan halal.",
        "Format JSON array:",
        "[{",
        "  \"nama\": \"nama atau kategori produk alternatif\",",
        "  \"alasan\": \"kenapa lebih baik (1 kalimat)\",",
        "  \"tips\": \"tips memilih di supermarket\",",
        "  \"halal\": \"HALAL\"",
        "}]"
      ], "E8F8F2", GREEN),
      sp(16),

      // ═══════════════════════════════════════════
      // BAB 8: KEAMANAN & BEST PRACTICES
      // ═══════════════════════════════════════════
      heading1("BAB 8 — Keamanan, Best Practices & Pengembangan Lanjut"),
      sp(),
      heading2("8.1  Keamanan API Key"),
      bulletItem("SELALU simpan API key di local.properties, JANGAN pernah hardcode langsung di source code"),
      bulletItem("Tambahkan local.properties ke file .gitignore agar tidak ter-upload ke GitHub"),
      bulletItem("Gunakan BuildConfig untuk mengakses key di kode: BuildConfig.GROQ_API_KEY"),
      bulletItem("Untuk produksi, pertimbangkan menggunakan backend proxy sederhana agar key tidak terekspos di APK"),
      sp(8),
      heading2("8.2  Error Handling"),
      bulletItem("Selalu tangani kasus produk tidak ditemukan di Open Food Facts (response.status != 1)"),
      bulletItem("Tangani kasus AI gagal merespons atau timeout — tampilkan hasil dari DB lokal saja"),
      bulletItem("Tangani kasus JSON parsing gagal dari respons AI — return default value yang aman"),
      bulletItem("Tampilkan pesan error yang ramah pengguna, bukan stack trace atau kode error teknis"),
      sp(8),
      heading2("8.3  Catatan Penting untuk Pengguna"),
      infoBox("Disclaimer Penting", [
        "Analisis halal dari Halalytics bersifat INFORMATIF dan berbasis AI, bukan merupakan",
        "sertifikasi resmi dari BPJPH atau MUI. Untuk kepastian halal yang resmi dan terjamin,",
        "selalu cari produk dengan label halal resmi dari Badan Penyelenggara Jaminan Produk",
        "Halal (BPJPH) atau sertifikat MUI yang masih berlaku.",
        "",
        "Analisis kesehatan juga bersifat umum dan tidak menggantikan saran dokter atau ahli gizi."
      ], "FEF6E7", AMBER),
      sp(12),

      heading2("8.4  Roadmap Pengembangan Lanjut"),
      twoColTable([
        ["Fitur", "Deskripsi"],
        ["Database Produk Lokal", "Tambahkan produk UMKM Indonesia yang belum ada di Open Food Facts"],
        ["Integrasi BPJPH API", "Cek sertifikat halal resmi BPJPH secara real-time jika API tersedia"],
        ["History Scan", "Simpan riwayat produk yang pernah di-scan menggunakan Room Database"],
        ["Favorit & Daftar", "Pengguna bisa menyimpan produk favorit atau membuat daftar belanja halal"],
        ["Scan Foto Bahan", "OCR untuk membaca bahan dari foto kemasan produk tanpa barcode"],
        ["Mode Offline Penuh", "Cache data produk yang sudah pernah di-scan untuk digunakan offline"],
        ["Notifikasi Recall", "Peringatan jika produk yang pernah di-scan ditarik dari peredaran"],
        ["Web Version", "Versi web menggunakan React + Next.js untuk aksesibilitas lebih luas"],
      ], 3200),
      sp(16),

      // ═══════════════════════════════════════════
      // PENUTUP
      // ═══════════════════════════════════════════
      new Paragraph({
        spacing: { after: 200 },
        border: { top: { style: BorderStyle.SINGLE, size: 8, color: GREEN, space: 1 } },
        children: [new TextRun("")]
      }),
      sp(8),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 160 },
        children: [new TextRun({ text: "HALALYTICS", font: "Arial", size: 36, bold: true, color: DARKGREEN })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 120 },
        children: [new TextRun({ text: "Satu Platform, Keputusan Tepat.", font: "Arial", size: 24, italics: true, color: GREEN })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 80 },
        children: [new TextRun({ text: "Membantu konsumen Muslim Indonesia berbelanja dengan lebih cerdas,", font: "Arial", size: 22, color: DARKGRAY })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 400 },
        children: [new TextRun({ text: "lebih sehat, dan lebih tenang dalam menjaga kehalalan produk.", font: "Arial", size: 22, color: DARKGRAY })]
      }),

    ]
  }]
});

Packer.toBuffer(doc).then(buffer => {
  fs.writeFileSync('/home/daffarizky/Project Halalytics/Halalytics_Penjelasan_Lengkap.docx', buffer);
  console.log('Dokumen berhasil dibuat!');
}).catch(err => {
  console.error('Error:', err);
  process.exit(1);
});
