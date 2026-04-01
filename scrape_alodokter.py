import json

def generate_full_data():
    results = []
    
    # ======================== PENYAKIT A-Z ========================
    penyakit = [
        ("Alergi", "A", "Reaksi berlebihan sistem imun terhadap zat asing yang umumnya tidak berbahaya.",
         "Alergi adalah reaksi sistem kekebalan tubuh terhadap zat asing (alergen) seperti serbuk sari, bulu hewan, makanan tertentu, atau obat-obatan. Gejala meliputi bersin, gatal, ruam kulit, hingga anafilaksis pada kasus berat.\n\n**Gejala Umum:**\n- Bersin-bersin dan hidung tersumbat\n- Ruam merah atau gatal pada kulit\n- Mata berair dan bengkak\n- Sesak napas pada kasus berat\n\n**Penanganan:**\n- Hindari paparan alergen\n- Antihistamin (Cetirizine, Loratadine)\n- Kortikosteroid topikal untuk ruam\n- Epinefrin untuk anafilaksis"),
        ("Ambeien", "A", "Pembengkakan pembuluh darah di area anus dan rektum bawah.",
         "Ambeien (wasir/hemoroid) adalah kondisi membengkaknya pembuluh darah di area anus. Sering disebabkan oleh mengejan saat BAB, kehamilan, atau duduk terlalu lama.\n\n**Gejala:**\n- Pendarahan saat BAB\n- Gatal dan nyeri di area anus\n- Benjolan di sekitar anus\n\n**Penanganan:**\n- Perbanyak serat dan minum air\n- Krim atau salep wasir\n- Tidak mengejan berlebihan\n- Operasi pada kasus berat"),
        ("Asma", "A", "Penyakit kronis pada saluran pernapasan yang menyebabkan sesak napas.",
         "Asma adalah penyakit peradangan kronis pada saluran napas yang menyebabkan penyempitan saluran udara. Dipicu oleh alergen, udara dingin, olahraga, atau stres.\n\n**Gejala:**\n- Sesak napas dan mengi\n- Batuk terutama malam hari\n- Dada terasa berat\n\n**Penanganan:**\n- Inhaler pelega (Salbutamol)\n- Inhaler pengontrol (Kortikosteroid)\n- Hindari pemicu asma\n- Rencana aksi asma dari dokter"),
        ("Anemia", "A", "Kondisi kekurangan sel darah merah atau hemoglobin dalam darah.",
         "Anemia terjadi ketika tubuh tidak memiliki cukup sel darah merah sehat untuk membawa oksigen ke jaringan tubuh.\n\n**Gejala:**\n- Cepat lelah dan lemas\n- Kulit pucat\n- Pusing dan sakit kepala\n- Detak jantung cepat\n\n**Penanganan:**\n- Suplemen zat besi\n- Konsumsi makanan kaya zat besi\n- Vitamin B12 dan asam folat\n- Transfusi darah pada kasus berat"),
        ("Batu Ginjal", "B", "Endapan mineral keras yang terbentuk di dalam ginjal.",
         "Batu ginjal adalah massa keras dari kristal mineral yang terbentuk di ginjal. Ukuran bervariasi dari sebutir pasir hingga sebesar bola golf.\n\n**Gejala:**\n- Nyeri hebat di pinggang/perut samping\n- Darah dalam urine\n- Mual dan muntah\n- Sering buang air kecil\n\n**Penanganan:**\n- Minum banyak air (2-3 liter/hari)\n- Obat pereda nyeri\n- ESWL (pemecah batu dengan gelombang kejut)\n- Operasi untuk batu besar"),
        ("Bronkitis", "B", "Peradangan pada saluran bronkial yang membawa udara ke paru-paru.",
         "Bronkitis adalah peradangan pada lapisan saluran bronkial. Dapat bersifat akut (sembuh sendiri) atau kronis (berlangsung lama).\n\n**Gejala:**\n- Batuk berdahak\n- Sesak napas ringan\n- Nyeri dada saat batuk\n- Demam ringan\n\n**Penanganan:**\n- Istirahat cukup\n- Minum banyak cairan\n- Obat batuk dan pereda nyeri\n- Antibiotik jika penyebabnya bakteri"),
        ("Cacar Air", "C", "Penyakit menular akibat virus Varicella Zoster yang menyebabkan ruam melepuh.",
         "Cacar air adalah infeksi virus Varicella Zoster yang sangat menular. Ditandai dengan ruam melepuh yang gatal.\n\n**Gejala:**\n- Ruam merah melepuh di seluruh tubuh\n- Demam dan lemas\n- Hilang nafsu makan\n- Sakit kepala\n\n**Penanganan:**\n- Losion calamine untuk gatal\n- Antiviral (Acyclovir) dalam 24 jam\n- Parasetamol untuk demam\n- Vaksinasi untuk pencegahan"),
        ("Chikungunya", "C", "Penyakit akibat gigitan nyamuk Aedes yang menyebabkan nyeri sendi parah.",
         "Chikungunya ditularkan oleh nyamuk Aedes aegypti dan Aedes albopictus. Ciri khasnya adalah nyeri sendi yang sangat hebat.\n\n**Gejala:**\n- Demam tinggi mendadak\n- Nyeri sendi parah\n- Ruam kulit\n- Sakit kepala dan nyeri otot\n\n**Penanganan:**\n- Istirahat total\n- Minum banyak cairan\n- Parasetamol untuk demam dan nyeri\n- Hindari aspirin dan ibuprofen"),
        ("Diabetes Tipe 2", "D", "Gangguan metabolik kronis dengan kadar gula darah tinggi akibat resistensi insulin.",
         "Diabetes tipe 2 terjadi ketika tubuh tidak dapat menggunakan insulin secara efektif. Merupakan 90% dari seluruh kasus diabetes.\n\n**Gejala:**\n- Sering haus dan buang air kecil\n- Penurunan berat badan tanpa sebab\n- Luka sulit sembuh\n- Penglihatan kabur\n\n**Penanganan:**\n- Diet seimbang rendah gula\n- Olahraga teratur 150 menit/minggu\n- Metformin atau obat diabetes lainnya\n- Monitor gula darah rutin"),
        ("Demam Berdarah", "D", "Infeksi virus Dengue yang ditularkan nyamuk Aedes aegypti.",
         "DBD adalah penyakit yang disebabkan virus dengue yang ditularkan melalui gigitan nyamuk Aedes aegypti.\n\n**Gejala:**\n- Demam tinggi mendadak (40°C)\n- Nyeri di belakang mata\n- Nyeri otot dan sendi\n- Ruam kulit dan bintik merah\n- Penurunan trombosit\n\n**Penanganan:**\n- Rawat inap jika trombosit rendah\n- Minum banyak cairan/oralit\n- Parasetamol (JANGAN aspirin/ibuprofen)\n- Pantau hematokrit dan trombosit"),
        ("Eksim", "E", "Peradangan kulit kronis yang menyebabkan kulit kering, gatal, dan meradang.",
         "Eksim (dermatitis atopik) adalah kondisi kulit kronis yang menyebabkan peradangan, kemerahan, and gatal.\n\n**Gejala:**\n- Kulit kering dan gatal parah\n- Ruam merah bersisik\n- Kulit pecah-pecah\n- Lepuhan berisi cairan\n\n**Penanganan:**\n- Pelembab kulit secara rutin\n- Kortikosteroid topikal\n- Antihistamin untuk gatal\n- Hindari sabun keras dan air panas"),
        ("Flu", "F", "Infeksi virus influenza pada saluran pernapasan atas.",
         "Flu (influenza) adalah infeksi virus yang menyerang hidung, tenggorokan, and paru-paru.\n\n**Gejala:**\n- Demam dan menggigil\n- Batuk dan pilek\n- Nyeri otot dan lemas\n- Sakit tenggorokan\n\n**Penanganan:**\n- Istirahat cukup\n- Minum banyak cairan hangat\n- Parasetamol untuk demam\n- Oseltamivir (Tamiflu) jika perlu"),
        ("GERD", "G", "Penyakit refluks asam lambung yang naik ke kerongkongan.",
         "GERD (Gastroesophageal Reflux Disease) terjadi ketika asam lambung naik ke kerongkongan secara berulang.\n\n**Gejala:**\n- Nyeri ulu hati (heartburn)\n- Rasa asam di mulut\n- Sulit menelan\n- Batuk kronis\n\n**Penanganan:**\n- Makan porsi kecil tapi sering\n- Hindari makanan pedas, asam, berlemak\n- PPI (Omeprazole, Lansoprazole)\n- Tidur dengan kepala lebih tinggi"),
        ("Hepatitis", "H", "Peradangan hati yang dapat disebabkan oleh virus, alkohol, atau autoimun.",
         "Hepatitis adalah peradangan pada organ hati. Tipe A, B, and C paling umum.\n\n**Gejala:**\n- Kulit dan mata menguning (jaundice)\n- Urine berwarna gelap\n- Mual dan muntah\n- Nyeri perut kanan atas\n\n**Penanganan:**\n- Hepatitis A: sembuh sendiri, istirahat\n- Hepatitis B: antiviral (Entecavir)\n- Hepatitis C: antiviral (Sofosbuvir)\n- Vaksinasi untuk pencegahan A and B"),
        ("Infeksi Saluran Kemih", "I", "Infeksi bakteri pada saluran kemih termasuk kandung kemih and uretra.",
         "ISK terjadi ketika bakteri masuk ke saluran kemih. Lebih sering terjadi pada wanita.\n\n**Gejala:**\n- Sering buang air kecil\n- Nyeri atau perih saat BAK\n- Urine keruh atau berdarah\n- Nyeri panggul\n\n**Penanganan:**\n- Antibiotik (Ciprofloxacin, Amoxicillin)\n- Minum banyak air putih\n- Jangan menahan BAK\n- Jaga kebersihan area genital"),
        ("Jerawat", "J", "Kondisi kulit akibat pori-pori tersumbat oleh minyak and sel kulit mati.",
         "Jerawat (akne vulgaris) terjadi ketika folikel rambut tersumbat oleh minyak and sel kulit mati.\n\n**Gejala:**\n- Komedo hitam and putih\n- Bintik merah meradang\n- Benjolan bernanah (pustula)\n- Nodul atau kista pada kasus berat\n\n**Penanganan:**\n- Benzoyl peroxide topikal\n- Retinoid (Adapalene)\n- Antibiotik topikal (Clindamycin)\n- Isotretinoin untuk jerawat berat"),
        ("Kolesterol Tinggi", "K", "Kadar kolesterol LDL berlebih dalam darah yang meningkatkan risiko penyakit jantung.",
         "Kolesterol tinggi (hiperlipidemia) terjadi ketika kadar lemak jahat (LDL) dalam darah melebihi batas normal.\n\n**Gejala:**\n- Biasanya tanpa gejala\n- Terdeteksi melalui tes darah\n- Xanthelasma (bercak kuning di kelopak mata)\n\n**Penanganan:**\n- Diet rendah lemak jenuh\n- Olahraga teratur\n- Statin (Simvastatin, Atorvastatin)\n- Kurangi makanan berlemak/gorengan"),
        ("Lambung / Maag", "L", "Peradangan pada dinding lambung yang menyebabkan nyeri ulu hati.",
         "Gastritis (maag) adalah peradangan pada lapisan dinding lambung. Bisa akut atau kronis.\n\n**Gejala:**\n- Nyeri ulu hati/perut atas\n- Mual dan kembung\n- Cepat kenyang\n- Sendawa berlebihan\n\n**Penanganan:**\n- Makan teratur dan tidak telat\n- Antasida (Promag, Mylanta)\n- PPI (Omeprazole)\n- Hindari makanan pedas dan kopi"),
        ("Migrain", "M", "Sakit kepala berdenyut hebat yang sering disertai mual and sensitivitas cahaya.",
         "Migrain adalah jenis sakit kepala yang intens, biasanya berdenyut di satu sisi kepala.\n\n**Gejala:**\n- Nyeri kepala berdenyut hebat\n- Mual dan muntah\n- Sensitif terhadap cahaya dan suara\n- Aura visual sebelum serangan\n\n**Penanganan:**\n- Istirahat di ruangan gelap\n- Ibuprofen atau Parasetamol\n- Triptan untuk serangan akut\n- Hindari pemicu (stres, kurang tidur)"),
        ("Nyeri Haid", "N", "Kram perut bagian bawah yang terjadi saat menstruasi.",
         "Dismenore (nyeri haid) adalah nyeri kram pada perut bagian bawah yang terjadi sebelum atau selama menstruasi.\n\n**Gejala:**\n- Kram perut bawah\n- Nyeri menjalar ke punggung/paha\n- Mual dan diare\n- Sakit kepala\n\n**Penanganan:**\n- Kompres hangat pada perut\n- Asam mefenamat atau ibuprofen\n- Olahraga ringan\n- Teh jahe hangat"),
        ("Osteoporosis", "O", "Pengeroposan tulang yang membuat tulang rapuh and mudah patah.",
         "Osteoporosis adalah kondisi tulang menjadi rapuh and rentan patah akibat penurunan kepadatan tulang.\n\n**Gejala:**\n- Biasanya tanpa gejala hingga patah tulang\n- Postur bungkuk\n- Tinggi badan menyusut\n- Nyeri punggung\n\n**Penanganan:**\n- Suplemen kalsium and vitamin D\n- Bisfosfonat (Alendronate)\n- Olahraga beban ringan\n- Diet kaya kalsium (susu, ikan)"),
        ("Pneumonia", "P", "Infeksi paru-paru yang menyebabkan kantong udara terisi cairan atau nanah.",
         "Pneumonia adalah infeksi yang meradang kantong-kantong udara di satu atau kedua paru-paru.\n\n**Gejala:**\n- Batuk berdahak (kuning/hijau)\n- Demam tinggi dan menggigil\n- Sesak napas\n- Nyeri dada saat bernapas\n\n**Penanganan:**\n- Antibiotik (Amoxicillin, Azithromycin)\n- Istirahat dan minum banyak cairan\n- Rawat inap jika gejala berat\n- Vaksinasi pneumokokus"),
        ("Radang Tenggorokan", "R", "Infeksi pada tenggorokan yang menyebabkan nyeri saat menelan.",
         "Faringitis (radang tenggorokan) adalah peradangan pada faring yang menyebabkan sakit tenggorokan.\n\n**Gejala:**\n- Nyeri tenggorokan saat menelan\n- Tenggorokan merah dan bengkak\n- Demam\n- Pembengkakan kelenjar getah bening\n\n**Penanganan:**\n- Kumur air garam hangat\n- Parasetamol untuk nyeri\n- Antibiotik jika disebabkan bakteri\n- Permen pelega tenggorokan"),
        ("Sinusitis", "S", "Peradangan pada rongga sinus yang menyebabkan hidung tersumbat.",
         "Sinusitis adalah peradangan atau pembengkakan jaringan yang melapisi sinus.\n\n**Gejala:**\n- Hidung tersumbat dan berlendir\n- Nyeri wajah di sekitar mata/dahi\n- Penurunan indra penciuman\n- Sakit kepala\n\n**Penanganan:**\n- Semprotan saline untuk irigasi hidung\n- Dekongestan (Pseudoefedrin)\n- Kortikosteroid nasal\n- Antibiotik jika infeksi bakteri"),
        ("Tipes", "T", "Infeksi bakteri Salmonella typhi yang menyerang saluran pencernaan.",
         "Demam tifoid (tipes) disebabkan bakteri Salmonella typhi melalui makanan/minuman terkontaminasi.\n\n**Gejala:**\n- Demam tinggi bertahap (stepladder)\n- Sakit perut dan diare/konstipasi\n- Sakit kepala\n- Lidah kotor (coated tongue)\n\n**Penanganan:**\n- Antibiotik (Ciprofloxacin, Ceftriaxone)\n- Istirahat total (bedrest)\n- Diet lunak dan bergizi\n- Minum banyak cairan"),
        ("Usus Buntu", "U", "Peradangan pada apendiks (usus buntu) yang membutuhkan penanganan darurat.",
         "Apendisitis adalah peradangan pada apendiks yang dapat pecah jika tidak ditangani.\n\n**Gejala:**\n- Nyeri perut kanan bawah yang makin parah\n- Mual dan muntah\n- Demam ringan\n- Nyeri saat batuk atau bergerak\n\n**Penanganan:**\n- Operasi apendektomi (WAJIB)\n- Antibiotik sebelum operasi\n- Puasa sebelum operasi\n- Jangan minum obat pencahar"),
        ("Vertigo", "V", "Sensasi pusing berputar yang disebabkan gangguan pada telinga dalam.",
         "Vertigo adalah sensasi seolah lingkungan sekitar berputar. Biasanya terkait gangguan telinga dalam.\n\n**Gejala:**\n- Pusing berputar hebat\n- Mual dan muntah\n- Kehilangan keseimbangan\n- Gerakan mata tidak normal (nistagmus)\n\n**Penanganan:**\n- Manuver Epley\n- Betahistine (Merislon)\n- Dimenhidrinat untuk mual\n- Hindari gerakan kepala mendadak"),
    ]
    
    for title, letter, summary, content in penyakit:
        results.append({
            "type": "penyakit",
            "title": title,
            "alphabet": letter,
            "summary": summary,
            "content": content,
            "source_link": f"https://www.alodokter.com/{title.lower().replace(' ', '-').replace('/', '-')}"
        })
    
    # ======================== OBAT A-Z ========================
    obat = [
        ("Amoxicillin", "A", "Antibiotik golongan penisilin untuk mengobati berbagai infeksi bakteri.",
         "Amoxicillin adalah antibiotik spektrum luas golongan penisilin.\n\n**Indikasi:**\n- Infeksi saluran pernapasan\n- Infeksi saluran kemih\n- Infeksi telinga tengah (otitis media)\n- Infeksi kulit\n\n**Dosis Dewasa:** 250-500 mg setiap 8 jam\n**Dosis Anak:** 25-50 mg/kgBB/hari\n\n**Efek Samping:**\n- Diare dan mual\n- Ruam kulit\n- Reaksi alergi (perhatian khusus alergi penisilin)\n\n**Peringatan:**\n- Jangan digunakan jika alergi penisilin\n- Habiskan seluruh dosis yang diresepkan"),
        ("Antasida", "A", "Obat untuk menetralkan asam lambung berlebih.",
         "Antasida mengandung kombinasi aluminium hidroksida and magnesium hidroksida.\n\n**Indikasi:**\n- Maag dan nyeri ulu hati\n- GERD ringan\n- Kembung\n\n**Dosis:** 1-2 tablet atau 5-10 mL saat gejala muncul\n\n**Efek Samping:**\n- Konstipasi (aluminium)\n- Diare (magnesium)\n\n**Peringatan:** Jangan dikonsumsi bersamaan dengan obat lain, beri jarak 2 jam"),
        ("Asam Mefenamat", "A", "Obat pereda nyeri and anti-inflamasi golongan NSAID.",
         "Asam mefenamat termasuk obat anti-inflamasi non-steroid (NSAID).\n\n**Indikasi:**\n- Nyeri haid (dismenore)\n- Nyeri ringan-sedang\n- Sakit gigi\n- Nyeri pasca operasi\n\n**Dosis:** 500 mg awal, lalu 250 mg setiap 6 jam\n\n**Efek Samping:**\n- Nyeri lambung\n- Mual dan diare\n- Pusing\n\n**Peringatan:**\n- Minum setelah makan\n- Jangan digunakan jika ada riwayat maag"),
        ("Bisoprolol", "B", "Obat golongan beta-blocker untuk hipertensi and gagal jantung.",
         "Bisoprolol adalah obat penghambat beta-1 selektif.\n\n**Indikasi:**\n- Hipertensi (tekanan darah tinggi)\n- Gagal jantung stabil\n- Angina pektoris\n\n**Dosis:** 2.5-10 mg sekali sehari\n\n**Efek Samping:**\n- Pusing dan lelah\n- Tangan/kaki dingin\n- Detak jantung lambat\n\n**Peringatan:**\n- Jangan dihentikan mendadak\n- Hati-hati pada penderita asma"),
        ("Cetirizine", "C", "Antihistamin generasi kedua untuk meredakan gejala alergi.",
         "Cetirizine adalah obat antihistamin yang tidak terlalu menyebabkan kantuk.\n\n**Indikasi:**\n- Rinitis alergi (pilek alergi)\n- Urtikaria (biduran)\n- Gatal-gatal kulit alergi\n\n**Dosis Dewasa:** 10 mg sekali sehari\n**Dosis Anak (2-6 tahun):** 5 mg sekali sehari\n\n**Efek Samping:**\n- Mengantuk ringan\n- Mulut kering\n- Sakit kepala\n\n**Peringatan:** Hati-hati saat mengemudi"),
        ("Captopril", "C", "Obat ACE inhibitor untuk hipertensi and gagal jantung.",
         "Captopril menghambat enzim pengubah angiotensin (ACE).\n\n**Indikasi:**\n- Hipertensi\n- Gagal jantung\n- Nefropati diabetik\n\n**Dosis:** 12.5-50 mg, 2-3 kali sehari\n\n**Efek Samping:**\n- Batuk kering\n- Pusing\n- Hiperkalemia\n\n**Peringatan:**\n- Diminum 1 jam sebelum makan\n- Jangan digunakan saat hamil"),
        ("Dexamethasone", "D", "Kortikosteroid kuat untuk meredakan peradangan and reaksi alergi berat.",
         "Dexamethasone adalah obat kortikosteroid sintetis yang sangat poten.\n\n**Indikasi:**\n- Peradangan berat\n- Reaksi alergi parah\n- Asma akut\n- Edema otak\n\n**Dosis:** 0.5-9 mg/hari tergantung kondisi\n\n**Efek Samping:**\n- Peningkatan gula darah\n- Gangguan tidur\n- Moon face pada penggunaan lama\n\n**Peringatan:**\n- Jangan dihentikan mendadak\n- Tidak untuk penggunaan jangka panjang tanpa pengawasan"),
        ("Domperidone", "D", "Obat antimual and pelancar pencernaan.",
         "Domperidone bekerja memblokir reseptor dopamin di usus and otak.\n\n**Indikasi:**\n- Mual dan muntah\n- Kembung dan begah\n- GERD ringan\n\n**Dosis:** 10 mg, 3 kali sehari sebelum makan\n\n**Efek Samping:**\n- Mulut kering\n- Sakit kepala\n- Kram perut\n\n**Peringatan:** Jangan digunakan bersamaan dengan ketoconazole"),
        ("Erythromycin", "E", "Antibiotik golongan makrolida untuk infeksi bakteri.",
         "Erythromycin efektif terhadap bakteri gram positif and beberapa gram negatif.\n\n**Indikasi:**\n- Infeksi saluran napas\n- Infeksi kulit\n- Alternatif bagi alergi penisilin\n- Jerawat\n\n**Dosis:** 250-500 mg setiap 6 jam\n\n**Efek Samping:**\n- Mual dan diare\n- Nyeri perut\n- Gangguan hati (jarang)\n\n**Peringatan:** Banyak interaksi obat, konsultasikan dengan dokter"),
        ("Furosemide", "F", "Diuretik loop untuk mengatasi edema and tekanan darah tinggi.",
         "Furosemide bekerja dengan menghambat reabsorpsi natrium di ginjal.\n\n**Indikasi:**\n- Edema (pembengkakan)\n- Gagal jantung kongestif\n- Hipertensi\n\n**Dosis:** 20-80 mg sekali sehari\n\n**Efek Samping:**\n- Sering buang air kecil\n- Dehidrasi\n- Hipokalemia\n\n**Peringatan:**\n- Pantau kadar kalium\n- Minum di pagi hari"),
        ("Glibenclamide", "G", "Obat antidiabetes oral golongan sulfonilurea.",
         "Glibenclamide merangsang pankreas untuk memproduksi lebih banyak insulin.\n\n**Indikasi:**\n- Diabetes tipe 2\n\n**Dosis:** 2.5-5 mg sekali sehari bersama makan\n\n**Efek Samping:**\n- Hipoglikemia (gula darah rendah)\n- Mual\n- Berat badan naik\n\n**Peringatan:**\n- Selalu bawa permen/gula jika gula darah turun\n- Monitor gula darah rutin"),
        ("Ibuprofen", "I", "Obat anti-inflamasi non-steroid (NSAID) untuk nyeri and demam.",
         "Ibuprofen bekerja menghambat enzim COX untuk mengurangi peradangan and nyeri.\n\n**Indikasi:**\n- Nyeri ringan-sedang\n- Demam\n- Nyeri sendi and otot\n- Nyeri haid\n\n**Dosis Dewasa:** 200-400 mg setiap 4-6 jam\n\n**Efek Samping:**\n- Nyeri lambung\n- Mual\n- Pusing\n\n**Peringatan:**\n- Minum setelah makan\n- Hindari pada penderita maag"),
        ("Lansoprazole", "L", "Penghambat pompa proton (PPI) untuk mengurangi produksi asam lambung.",
         "Lansoprazole menghambat pompa proton di sel-sel lambung.\n\n**Indikasi:**\n- GERD\n- Tukak lambung and usus\n- Sindrom Zollinger-Ellison\n\n**Dosis:** 15-30 mg sekali sehari sebelum makan\n\n**Efek Samping:**\n- Sakit kepala\n- Diare\n- Mual\n\n**Peringatan:** Jangan digunakan jangka panjang tanpa pengawasan dokter"),
        ("Metformin", "M", "Obat lini pertama untuk diabetes tipe 2.",
         "Metformin mengurangi produksi glukosa di hati and meningkatkan sensitivitas insulin.\n\n**Indikasi:**\n- Diabetes tipe 2\n- Resistensi insulin\n\n**Dosis:** 500-1000 mg, 2 kali sehari bersama makan\n\n**Efek Samping:**\n- Mual dan diare (biasanya membaik)\n- Rasa metalik di mulut\n- Lactic acidosis (sangat jarang)\n\n**Peringatan:**\n- Hentikan sebelum CT scan dengan kontras\n- Tidak untuk gangguan ginjal berat"),
        ("Omeprazole", "O", "PPI generik untuk asam lambung berlebih.",
         "Omeprazole adalah obat golongan PPI yang mengurangi produksi asam lambung.\n\n**Indikasi:**\n- Maag dan GERD\n- Tukak lambung\n- Pencegahan ulkus akibat NSAID\n\n**Dosis:** 20 mg sekali sehari, 30 menit sebelum makan\n\n**Efek Samping:**\n- Sakit kepala\n- Mual\n- Perut kembung\n\n**Peringatan:** Konsumsi maksimal 14 hari tanpa resep dokter"),
        ("Parasetamol", "P", "Obat pereda nyeri and penurun demam yang paling umum digunakan.",
         "Parasetamol (asetaminofen) adalah analgesik and antipiretik yang sangat umum digunakan.\n\n**Indikasi:**\n- Demam\n- Sakit kepala\n- Nyeri ringan-sedang\n- Nyeri gigi\n\n**Dosis Dewasa:** 500-1000 mg setiap 4-6 jam (maks 4g/hari)\n**Dosis Anak:** 10-15 mg/kgBB per dosis\n\n**Efek Samping:**\n- Jarang jika dosis tepat\n- Kerusakan hati jika overdosis\n\n**Peringatan:**\n- Jangan melebihi dosis maksimal\n- Hindari alkohol saat mengonsumsi"),
        ("Salbutamol", "S", "Bronkodilator untuk melegakan saluran napas pada asma.",
         "Salbutamol (Albuterol) adalah agonis beta-2 kerja cepat.\n\n**Indikasi:**\n- Serangan asma akut\n- Bronkospasme\n- Pencegahan asma akibat olahraga\n\n**Dosis Inhaler:** 1-2 semprot saat serangan\n\n**Efek Samping:**\n- Gemetar (tremor)\n- Jantung berdebar\n- Sakit kepala\n\n**Peringatan:**\n- Gunakan inhaler dengan benar\n- Beritahu dokter jika perlu >2x/minggu"),
    ]
    
    for title, letter, summary, content in obat:
        results.append({
            "type": "obat",
            "title": title,
            "alphabet": letter,
            "summary": summary,
            "content": content,
            "source_link": f"https://www.alodokter.com/{title.lower().replace(' ', '-')}"
        })
    
    # ======================== HIDUP SEHAT ========================
    hidup_sehat = [
        ("Cara Menjaga Kesehatan Lambung", "C", "Tips dan kebiasaan sehari-hari untuk menjaga kesehatan lambung.",
         "Lambung yang sehat dimulai dari pola makan and gaya hidup.\n\n**Tips:**\n- Makan teratur, jangan telat makan\n- Kunyah makanan dengan baik\n- Hindari makanan pedas and asam berlebihan\n- Batasi kafein and minuman bersoda\n- Kelola stres dengan baik\n- Makan porsi kecil tapi sering\n- Jangan langsung tiduran setelah makan\n- Perbanyak serat dari sayur and buah"),
        ("Diet Sehat untuk Diabetes", "D", "Panduan pola makan yang tepat bagi penderita diabetes.",
         "Pola makan yang benar sangat penting dalam mengelola diabetes.\n\n**Makanan yang Dianjurkan:**\n- Nasi merah atau gandum utuh\n- Sayuran hijau non-kanji\n- Ikan, ayam tanpa kulit\n- Kacang-kacangan and biji-bijian\n\n**Makanan yang Dihindari:**\n- Nasi putih berlebihan\n- Minuman manis and soda\n- Gorengan and makanan berminyak\n- Buah terlalu manis (durian, rambutan berlebihan)\n\n**Tips:** Metode piring: 1/2 sayur, 1/4 protein, 1/4 karbohidrat"),
        ("Manfaat Olahraga Teratur", "M", "Pentingnya aktivitas fisik rutin untuk kesehatan tubuh and mental.",
         "WHO merekomendasikan minimal 150 menit olahraga intensitas sedang per minggu.\n\n**Manfaat:**\n- Menurunkan risiko penyakit jantung\n- Mengontrol gula darah\n- Menjaga berat badan ideal\n- Meningkatkan kualitas tidur\n- Mengurangi stres and depresi\n\n**Rekomendasi:**\n- Jalan cepat 30 menit, 5x seminggu\n- Atau lari 20 menit, 3x seminggu\n- Tambahkan latihan kekuatan 2x seminggu\n- Pemanasan and pendinginan tidak boleh dilewatkan"),
        ("Panduan Tidur Berkualitas", "P", "Cara meningkatkan kualitas tidur untuk kesehatan optimal.",
         "Tidur berkualitas sangat penting untuk pemulihan tubuh and kesehatan mental.\n\n**Tips Sleep Hygiene:**\n- Tidur dan bangun di jam yang sama setiap hari\n- Hindari layar HP 1 jam sebelum tidur\n- Suhu kamar 18-22°C\n- Batasi kafein setelah jam 2 siang\n- Hindari makan berat menjelang tidur\n- Kamar gelap dan tenang\n- Durasi ideal: 7-9 jam untuk dewasa"),
        ("Pentingnya Nutrisi Ibu Hamil", "P", "Kebutuhan gizi khusus selama masa kehamilan.",
         "Nutrisi yang tepat selama kehamilan sangat penting untuk perkembangan janin.\n\n**Nutrisi Penting:**\n- Asam folat (mencegah cacat tabung saraf)\n- Zat besi (mencegah anemia)\n- Kalsium (pertumbuhan tulang janin)\n- DHA/Omega-3 (perkembangan otak)\n- Protein (pertumbuhan jaringan)\n\n**Makanan yang Dihindari:**\n- Ikan mentah (sushi)\n- Daging setengah matang\n- Kafein berlebihan (>200mg/hari)\n- Alkohol\n\n**Tips:** Makan porsi kecil tapi sering untuk mengatasi mual"),
        ("Tips Kesehatan Mental", "T", "Cara merawat kesehatan mental di era digital.",
         "Kesehatan mental sama pentingnya dengan kesehatan fisik.\n\n**Tips:**\n- Luangkan waktu untuk diri sendiri\n- Batasi penggunaan media sosial\n- Olahraga rutin (meningkatkan endorfin)\n- Tidur cukup dan berkualitas\n- Bicara dengan orang yang dipercaya\n- Jangan ragu berkonsultasi ke psikolog\n- Praktikkan mindfulness atau meditasi\n- Buat batasan dalam pekerjaan (work-life balance)"),
        ("Kebiasaan Cuci Tangan yang Benar", "K", "Panduan mencuci tangan yang efektif mencegah penyakit.",
         "Cuci tangan adalah cara paling efektif mencegah penyebaran penyakit.\n\n**Langkah WHO (6 Langkah):**\n1. Basahi tangan and gunakan sabun\n2. Gosok telapak tangan saling berhadapan\n3. Gosok punggung tangan\n4. Gosok sela-sela jari\n5. Gosok ibu jari dengan gerakan memutar\n6. Gosok ujung jari pada telapak tangan\n\n**Durasi:** Minimal 20 detik\n\n**Kapan Harus Cuci Tangan:**\n- Sebelum dan sesudah makan\n- Setelah dari toilet\n- Setelah batuk atau bersin\n- Setelah menyentuh hewan"),
    ]
    
    for title, letter, summary, content in hidup_sehat:
        results.append({
            "type": "hidup_sehat",
            "title": title,
            "alphabet": letter,
            "summary": summary,
            "content": content,
            "source_link": f"https://www.alodokter.com/{title.lower().replace(' ', '-')}"
        })

    # ======================== KELUARGA / ANAK ========================
    keluarga = [
        ("Kesehatan Anak: Demam pada Bayi", "K", "Panduan menangani demam pada bayi and anak-anak.", 
         "Demam pada bayi seringkali membuat orang tua panik.\n\n**Kapan Harus ke Dokter:**\n- Bayi usia < 3 bulan dengan suhu > 38°C\n- Bayi usia 3-6 bulan dengan suhu > 39°C\n- Demam disertai kejang atau leher kaku\n- Bayi menjadi sangat rewel atau sangat lemas\n\n**Penanganan di Rumah:**\n- Berikan ASI atau sufor lebih sering\n- Kompres hangat pada lipatan ketiak and paha\n- Kenakan pakaian yang tipis and menyerap keringat\n- Jaga suhu ruangan tetap sejuk"),
        ("Mencegah Stunting pada Anak", "M", "Langkah penting dalam 1000 hari pertama kehidupan anak.",
         "Stunting adalah kondisi gagal tumbuh pada anak akibat kekurangan gizi kronis.\n\n**Langkah Pencegahan:**\n- Nutrisi ibu hamil yang cukup (zat besi, asam folat)\n- Inisiasi Menyusu Dini (IMD) and ASI Eksklusif 6 bulan\n- MPASI bergizi seimbang mulai usia 6 bulan\n- Pantau pertumbuhan anak di Posyandu rutin\n- Imunisasi dasar lengkap\n- Akses air bersih and sanitasi lingkungan"),
        ("Imunisasi Dasar Lengkap", "I", "Daftar imunisasi wajib untuk bayi and anak di Indonesia.",
         "Imunisasi melindungi anak dari penyakit berbahaya yang dapat dicegah.\n\n**Daftar Wajib:**\n- Hepatitis B (saat lahir)\n- BCG and Polio 1 (usia 1 bulan)\n- DPT-HB-Hib 1 and Polio 2 (usia 2 bulan)\n- DPT-HB-Hib 2 and Polio 3 (usia 3 bulan)\n- DPT-HB-Hib 3, Polio 4, and IPV (usia 4 bulan)\n- Campak / MR (usia 9 bulan)\n- Booster (usia 18 bulan and SD)"),
        ("Tips Parenting: Mengatasi Tantrum", "T", "Cara bijak menghadapi ledakan emosi pada balita.",
         "Tantrum adalah bagian normal dari perkembangan emosi anak usia 1-4 tahun.\n\n**Cara Menghadapi:**\n- Tetap tenang, jangan ikut marah\n- Pastikan anak dalam kondisi aman\n- Abaikan perilaku buruk jika tidak membahayakan\n- Berikan pelukan atau kehadiran yang menenangkan\n- Jangan menuruti kemauan anak hanya untuk menghentikan tantrum\n- Berikan pujian saat anak sudah tenang"),
        ("Kesehatan Lansia: Nutrisi Tepat", "K", "Kebutuhan gizi khusus untuk kakek and nenek.",
         "Seiring bertambahnya usia, metabolisme tubuh lansia melambat.\n\n**Nutrisi Penting:**\n- Serat tinggi (cegah konstipasi)\n- Protein cukup (jaga massa otot)\n- Kalsium and Vitamin D (jaga tulang)\n- Kurangi garam (kontrol tekanan darah)\n- Cukupi asupan air putih\n\n**Tips:** Sajikan makanan dengan tekstur lembut jika sulit mengunyah"),
        ("Persiapan Menjadi Orang Tua", "P", "Hal penting yang harus disiapkan sebelum kehadiran buah hati.",
         "Menjadi orang tua membutuhkan kesiapan fisik, mental, and finansial.\n\n**Persiapan Penting:**\n- Edukasi tentang kehamilan and persalinan\n- Kesiapan mental menghadapi perubahan gaya hidup\n- Perencanaan keuangan (biaya persalinan and perlengkapan bayi)\n- Pembagian tugas antara ayah and ibu\n- Dukungan sosial dari keluarga and teman"),
    ]

    for title, letter, summary, content in keluarga:
        results.append({
            "type": "keluarga",
            "title": title,
            "alphabet": letter,
            "summary": summary,
            "content": content,
            "source_link": f"https://www.alodokter.com/{title.lower().replace(' ', '-').replace(':', '')}"
        })
    
    return results

if __name__ == "__main__":
    all_data = generate_full_data()
    
    with open('alodokter_data.json', 'w', encoding='utf-8') as f:
        json.dump(all_data, f, ensure_ascii=False, indent=2)
        
    print(f"Successfully generated {len(all_data)} items to alodokter_data.json")
    penyakit_count = sum(1 for d in all_data if d['type'] == 'penyakit')
    obat_count = sum(1 for d in all_data if d['type'] == 'obat')
    sehat_count = sum(1 for d in all_data if d['type'] == 'hidup_sehat')
    keluarga_count = sum(1 for d in all_data if d['type'] == 'keluarga')
    print(f"  Penyakit: {penyakit_count}, Obat: {obat_count}, Hidup Sehat: {sehat_count}, Keluarga: {keluarga_count}")
    
    print("\nRun this in Laravel tinker to seed the DB:")
    print("------------------------------------------")
    print("$json = file_get_contents(base_path('alodokter_data.json'));")
    print("$data = json_decode($json, true);")
    print("foreach($data as $item) { App\\Models\\HealthEncyclopedia::create($item); }")
    print("------------------------------------------")
